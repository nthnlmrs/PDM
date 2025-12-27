<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefundController extends Controller
{
    // Buyer requests refund
    public function store(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status === 'completed') {
            return back()->with('error', 'Cannot refund a completed order.');
        }

        $request->validate([
            'reason' => 'required|string',
        ]);

        Refund::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'reason' => $request->reason,
            'status' => 'pending',
        ]);
        
        $order->update(['status' => 'refunded']); // Or 'disputed' depending on flow. Let's use 'disputed' conceptually until resolved.
        // Actually for this flow, let's keep order status as 'pending' or 'refunded' to lock it? 
        // Let's mark order as 'disputed' to stop auto-complete.
        $order->update(['status' => 'disputed']);

        return back()->with('success', 'Refund requested.');
    }

    // Buyer escalates to Admin
    public function escalate(Refund $refund)
    {
        if ($refund->user_id !== Auth::id()) {
            abort(403);
        }

        if ($refund->status === 'rejected') {
            $refund->update(['status' => 'escalated']);
            return back()->with('success', 'Dispute escalated to Admin.');
        }

        return back()->with('error', 'Cannot escalate this refund.');
    }

    // Seller views refunds
    public function sellerIndex()
    {
        // Get refunds for products owned by this seller
        $refunds = Refund::whereHas('order.items.product', function ($q) {
            $q->where('user_id', Auth::id());
        })->with('order.items.product')->latest()->paginate(10);

        return view('seller.refunds.index', compact('refunds'));
    }

    // Seller approves or rejects
    public function sellerDecide(Request $request, Refund $refund)
    {
        // Verify ownership (simplified for single item orders)
        $sellerId = $refund->order->items->first()->product->user_id;
        if ($sellerId !== Auth::id()) {
            abort(403);
        }

        $action = $request->input('action'); // 'approve' or 'reject'

        if ($action === 'approve') {
            $refund->update(['status' => 'approved']);
            // Return money to buyer
            $buyer = $refund->user;
            $buyer->balance += $refund->order->total_amount;
            $buyer->save();
            
            $refund->order->update(['status' => 'refunded']);

        } elseif ($action === 'reject') {
            $refund->update(['status' => 'rejected', 'seller_response' => $request->input('response')]);
            // Order status remains disputed/pending, waiting for buyer to escalate or accept
        }

        return back()->with('success', 'Refund decision recorded.');
    }
}
