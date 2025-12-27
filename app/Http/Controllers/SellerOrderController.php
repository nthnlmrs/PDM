<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerOrderController extends Controller
{
    public function index()
    {
        // Get orders where items contain products belonging to the logged-in seller
        $orders = Order::whereHas('items.product', function ($query) {
            $query->where('user_id', Auth::id());
        })->with(['user', 'items' => function ($query) {
            $query->whereHas('product', function ($q) {
                $q->where('user_id', Auth::id());
            })->with('product');
        }])->latest()->paginate(10);

        return view('seller.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Verify seller owns products in this order
        $hasProducts = $order->items()->whereHas('product', function ($q) {
            $q->where('user_id', Auth::id());
        })->exists();

        if (!$hasProducts) {
            abort(403);
        }

        $order->load(['user', 'items.product']);

        return view('seller.orders.show', compact('order'));
    }

    public function updateTracking(Request $request, Order $order)
    {
        // Verify seller owns products in this order
        $hasProducts = $order->items()->whereHas('product', function ($q) {
            $q->where('user_id', Auth::id());
        })->exists();

        if (!$hasProducts) {
            abort(403);
        }

        $request->validate([
            'tracking_number' => 'required|string|max:100',
        ]);

        $order->update([
            'tracking_number' => $request->tracking_number,
            'status' => 'shipped',
        ]);

        return back()->with('success', 'Nomor resi berhasil diupdate!');
    }
}
