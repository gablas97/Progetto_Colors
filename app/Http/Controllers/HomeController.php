<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::active()
            ->rootCategories()
            ->withCount(['products' => fn ($q) => $q->active()])
            ->orderBy('order')
            ->limit(6)
            ->get();

        $featured = Product::active()
            ->where('is_featured', true)
            ->with(['variants' => fn ($q) => $q->active()])
            ->orderBy('order')
            ->limit(8)
            ->get();

        return view('home', compact('categories', 'featured'));
    }
}
