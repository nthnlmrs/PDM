<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $order = Order::findOrFail($request->order_id);

        // Verify order belongs to user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'This order does not belong to you.');
        }

        // Verify order is completed
        if ($order->status !== 'completed') {
            return back()->with('error', 'You can only review completed orders.');
        }

        // Verify product is in order
        $hasProduct = $order->items()->where('product_id', $product->id)->exists();
        if (!$hasProduct) {
            return back()->with('error', 'This product is not in your order.');
        }

        // Check if already reviewed
        $exists = Review::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->where('order_id', $order->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'You have already reviewed this product for this order.');
        }

        Review::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'order_id' => $order->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Review submitted successfully!');
    }

    public function update(Request $request, Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Review updated successfully!');
    }

    public function destroy(Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        $review->delete();

        return back()->with('success', 'Review deleted.');
    }
}
