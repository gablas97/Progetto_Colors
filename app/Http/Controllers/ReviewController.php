<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'title'   => 'nullable|string|max:100',
            'comment' => 'nullable|string|max:2000',
        ]);

        if (Review::where('product_id', $product->id)->where('user_id', auth()->id())->exists()) {
            return back()->with('review_error', 'Hai già lasciato una recensione per questo prodotto.');
        }

        $order = Order::where('user_id', auth()->id())
            ->where('status', 'delivered')
            ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
            ->first();

        Review::create([
            'product_id'           => $product->id,
            'user_id'              => auth()->id(),
            'order_id'             => $order?->id,
            'rating'               => $request->rating,
            'title'                => $request->title,
            'comment'              => $request->comment,
            'is_verified_purchase' => $order !== null,
            'is_approved'          => false,
            'reward_sent'          => false,
        ]);

        return back()->with('review_sent', true);
    }
}
