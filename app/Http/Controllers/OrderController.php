<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Auth::user()->orders()->with('items.product', 'refund')->latest()->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function store(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:wallet,apple_pay,paypal,credit_card',
        ]);

        $user = Auth::user();
        $quantity = $request->quantity;
        $totalPrice = ($product->discount_price ?? $product->price) * $quantity;
        
        // Check stock
        if ($product->stock < $quantity) {
            return back()->with('error', 'Insufficient stock.');
        }

        // Check balance if Wallet
        if ($request->payment_method === 'wallet' && $user->balance < $totalPrice) {
            return back()->with('error', 'Insufficient wallet balance.');
        }

        try {
            DB::transaction(function () use ($user, $product, $quantity, $totalPrice, $request) {
                // Deduct balance only if Wallet
                if ($request->payment_method === 'wallet') {
                    $user->balance -= $totalPrice;
                    $user->save();
                }

                // Deduct Stock
                $product->stock -= $quantity;
                $product->save();

                // Create Order
                $order = Order::create([
                    'user_id' => $user->id,
                    'invoice_number' => 'INV-' . strtoupper(Str::random(10)),
                    'total_amount' => $totalPrice,
                    'status' => 'pending',
                    'payment_method' => $request->payment_method,
                ]);

                // Create Item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'price' => $product->discount_price ?? $product->price,
                    'quantity' => $quantity,
                ]);
            });

            return redirect()->route('order.index')->with('success', 'Purchase successful!');
        } catch (\Exception $e) {
            return back()->with('error', 'Transaction failed: ' . $e->getMessage());
        }
    }

    public function confirm(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status === 'pending') {
            $order->update(['status' => 'completed']);
            
            // Transfer money to seller only if paid via Wallet (internal money)
            // If paid via ApplePay/PayPal, we assume platform received it and we add to seller balance now?
            // Or maybe separate logic. For simplicity, we add to seller balance regardless of payment method,
            // assuming the platform collected the money (simulated) and owes the seller.
            
            $seller = $order->items->first()->product->seller;
            $seller->balance += $order->total_amount;
            $seller->save();
        }

        return back()->with('success', 'Order confirmed.');
    }

    public function confirmReceipt(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->buyer_confirmed_at) {
            return back()->with('error', 'Pesanan sudah dikonfirmasi sebelumnya.');
        }

        if (!$order->tracking_number) {
            return back()->with('error', 'Menunggu seller menginput resi pengiriman.');
        }

        $order->update([
            'buyer_confirmed_at' => now(),
            'status' => 'completed',
        ]);

        // Transfer money to seller
        foreach ($order->items as $item) {
            $seller = $item->product->seller;
            $seller->balance += ($item->price * $item->quantity);
            $seller->save();
        }

        return back()->with('success', 'Pesanan dikonfirmasi diterima! Pembayaran telah ditransfer ke seller.');
    }
}
