<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()
            ->where('is_service', false)
            ->with(['brand', 'categories', 'variants' => fn ($q) => $q->active()])
            ->orderBy('order');

        $activeCategory = null;
        $activeRoot     = null;
        $subLevels      = [];

        if ($request->filled('categoria')) {
            $activeCategory = Category::active()->where('slug', $request->categoria)->first();
        }

        if ($activeCategory) {
            // Costruisce la catena antenati: [root, ..., activeCategory]
            $chain = [$activeCategory];
            $cat   = $activeCategory->parent;
            while ($cat) {
                array_unshift($chain, $cat);
                $cat = $cat->parent;
            }

            $activeRoot = $chain[0];

            // Filtra prodotti: appartengono alla categoria attiva o a qualsiasi discendente
            $filterIds = $this->collectDescendantIds($activeCategory)->push($activeCategory->id);
            $query->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $filterIds));

            // Costruisce i livelli di pill (uno per ogni antenato che ha figli)
            foreach ($chain as $i => $ancestor) {
                $children = $ancestor->children()->active()->orderBy('name')->get();
                if ($children->isEmpty()) {
                    break;
                }
                $subLevels[] = [
                    'items'      => $children,
                    'selected'   => $chain[$i + 1] ?? null,
                    'parentSlug' => $ancestor->slug,
                ];
            }
        }

        if ($request->filled('marca')) {
            $query->whereHas('brand', fn ($q) => $q->where('slug', $request->marca));
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('short_description', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%")
            );
        }

        $products       = $query->paginate(12)->withQueryString();
        $brands         = Brand::active()->orderBy('name')->get();
        $rootCategories = Category::active()->rootCategories()->get();

        return view('products.index', compact(
            'products', 'brands', 'activeCategory', 'activeRoot', 'subLevels', 'rootCategories'
        ));
    }

    private function collectDescendantIds(Category $category): Collection
    {
        $ids = collect();
        foreach ($category->children()->active()->get() as $child) {
            $ids->push($child->id);
            $ids = $ids->merge($this->collectDescendantIds($child));
        }
        return $ids;
    }

    public function show(string $slug)
    {
        $product = Product::active()
            ->where('is_service', false)
            ->where('slug', $slug)
            ->with([
                'brand',
                'categories',
                'variants' => fn ($q) => $q->active()->orderBy('order'),
                'images'   => fn ($q) => $q->orderBy('order'),
                'reviews'  => fn ($q) => $q->approved()->with('user')->latest(),
            ])
            ->firstOrFail();

        $product->incrementViews();

        $related = $this->getRelatedProducts($product);

        $averageRating = $product->reviews->avg('rating');
        $reviewsCount  = $product->reviews->count();

        $canReview          = false;
        $hasReceivedReward  = false;
        $alreadyReviewed    = false;

        if (auth()->check()) {
            $userId = auth()->id();

            $alreadyReviewed = Review::where('product_id', $product->id)
                ->where('user_id', $userId)
                ->exists();

            if (!$alreadyReviewed) {
                $canReview = Order::where('user_id', $userId)
                    ->where('status', 'delivered')
                    ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
                    ->exists();
            }

            $hasReceivedReward = Review::where('user_id', $userId)
                ->where('reward_sent', true)
                ->exists();
        }

        $wishlistIds = auth()->check()
            ? auth()->user()->wishlists()->pluck('product_id')->toArray()
            : [];

        return view('products.show', compact(
            'product', 'related',
            'averageRating', 'reviewsCount',
            'canReview', 'hasReceivedReward', 'alreadyReviewed',
            'wishlistIds'
        ));
    }

    private function getRelatedProducts(Product $product): Collection
    {
        $categoryIds    = $product->categories->pluck('id');
        $parentIds      = $product->categories->map(fn ($c) => $c->parent_id)->filter()->unique();
        $brandId        = $product->brand_id;

        // Recupera candidati: stessa categoria O stessa categoria padre
        $candidates = Product::active()
            ->where('products.id', '!=', $product->id)
            ->where(function ($q) use ($categoryIds, $parentIds) {
                $q->whereHas('categories', fn ($q2) => $q2->whereIn('categories.id', $categoryIds));
                if ($parentIds->isNotEmpty()) {
                    $q->orWhereHas('categories', fn ($q2) => $q2->whereIn('categories.parent_id', $parentIds));
                }
            })
            ->with(['variants' => fn ($q) => $q->active()])
            ->get();

        // Assegna punteggio e ordina
        return $candidates
            ->map(function ($p) use ($categoryIds, $brandId) {
                $score = 0;
                $productCatIds = $p->categories->pluck('id');

                // Stessa categoria foglia: +3
                if ($productCatIds->intersect($categoryIds)->isNotEmpty()) {
                    $score += 3;
                } else {
                    // Stessa categoria padre (già filtrato sopra ma meno rilevante): +1
                    $score += 1;
                }

                // Stesso brand: +2
                if ($brandId && $p->brand_id === $brandId) {
                    $score += 2;
                }

                $p->_score = $score;
                $p->_in_stock = $p->isInStock() ? 1 : 0;
                return $p;
            })
            ->sortBy([
                fn ($a, $b) => $b->_in_stock <=> $a->_in_stock, // disponibili prima
                fn ($a, $b) => $b->_score <=> $a->_score,        // poi per rilevanza
            ])
            ->values();
    }
}
