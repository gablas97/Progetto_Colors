<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()
            ->with(['brand', 'categories', 'variants' => fn ($q) => $q->active()])
            ->orderBy('order');

        if ($request->filled('categoria')) {
            $query->whereHas('categories', fn ($q) => $q->where('slug', $request->categoria));
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

        $products   = $query->paginate(12)->withQueryString();
        $categories = Category::active()->orderBy('name')->get();
        $brands     = Brand::active()->orderBy('name')->get();

        $activeCategory = $request->filled('categoria')
            ? Category::where('slug', $request->categoria)->first()
            : null;

        return view('products.index', compact('products', 'categories', 'brands', 'activeCategory'));
    }

    public function show(string $slug)
    {
        $product = Product::active()
            ->where('slug', $slug)
            ->with([
                'brand',
                'categories',
                'variants' => fn ($q) => $q->active()->orderBy('order'),
                'images'   => fn ($q) => $q->orderBy('order'),
            ])
            ->firstOrFail();

        $product->incrementViews();

        $related = Product::active()
            ->whereHas('categories', fn ($q) => $q->whereIn('id', $product->categories->pluck('id')))
            ->where('id', '!=', $product->id)
            ->with(['variants' => fn ($q) => $q->active()])
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'related'));
    }
}
