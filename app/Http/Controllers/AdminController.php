<?php

namespace App\Http\Controllers;

use App\Models\Refund;
use App\Models\SellerRequest;
use App\Models\User;
use App\Models\Product;
use App\Models\Ticket;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function users()
    {
        $users = User::paginate(20);
        return view('admin.users', compact('users'));
    }

    public function topup(Request $request, User $user)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'action' => 'required|in:add,deduct'
        ]);

        if ($request->action === 'add') {
            $user->balance += $request->amount;
            $message = 'Balance added successfully.';
        } else {
            if ($user->balance < $request->amount) {
                return back()->with('error', 'Insufficient user balance to deduct this amount.');
            }
            $user->balance -= $request->amount;
            $message = 'Balance deducted successfully.';
        }

        $user->save();
        return back()->with('success', $message);
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:user,seller,admin']);
        $user->update(['role' => $request->role]);
        return back()->with('success', 'Role updated.');
    }

    public function refunds()
    {
        $refunds = Refund::where('status', 'escalated')->with('order', 'user')->paginate(10);
        return view('admin.refunds', compact('refunds'));
    }

    public function resolveRefund(Request $request, Refund $refund)
    {
        $decision = $request->input('decision'); // 'refund_buyer' or 'pay_seller'

        if ($decision === 'refund_buyer') {
            $refund->update(['status' => 'resolved', 'admin_note' => 'Resolved in favor of buyer.']);
            $refund->user->balance += $refund->order->total_amount;
            $refund->user->save();
            $refund->order->update(['status' => 'refunded']);
        } else {
            $refund->update(['status' => 'resolved', 'admin_note' => 'Resolved in favor of seller.']);
            // Pay seller
            $seller = $refund->order->items->first()->product->seller;
            $seller->balance += $refund->order->total_amount;
            $seller->save();
            $refund->order->update(['status' => 'completed']);
        }

        return back()->with('success', 'Dispute resolved.');
    }

    public function requests()
    {
        $requests = SellerRequest::where('status', 'pending')->with('user')->paginate(10);
        return view('admin.requests', compact('requests'));
    }

    public function approveRequest(SellerRequest $request)
    {
        $request->update(['status' => 'approved']);
        $request->user->update([
            'role' => 'seller',
            'store_name' => $request->store_name,
            'store_description' => $request->store_description
        ]);
        return back()->with('success', 'User promoted to Seller.');
    }

    public function toggleSuspend(User $user)
    {
        if ($user->role === 'admin') return back()->with('error', 'Cannot suspend admin.');
        
        $user->is_suspended = !$user->is_suspended;
        $user->save();

        if ($user->is_suspended) {
            // Hide products
            Product::where('user_id', $user->id)->update(['status' => 'suspended']);
        }
        
        return back()->with('success', $user->is_suspended ? 'User suspended and products hidden.' : 'User unsuspended.');
    }

    public function products()
    {
        $products = Product::with('seller')->latest()->paginate(20);
        return view('admin.products', compact('products'));
    }

    public function toggleProductSuspend(Product $product)
    {
        $product->status = $product->status === 'active' ? 'suspended' : 'active';
        $product->save();
        return back()->with('success', 'Product status updated.');
    }

    public function tickets()
    {
        $tickets = Ticket::with('user')->where('status', 'open')->latest()->paginate(10);
        return view('admin.tickets', compact('tickets'));
    }

    public function resolveTicket(Request $request, Ticket $ticket)
    {
        $action = $request->input('action'); // 'unsuspend', 'close'

        if ($action === 'unsuspend') {
            $ticket->user->update(['is_suspended' => false]);
            $ticket->update(['status' => 'resolved', 'admin_response' => 'Unban request approved.']);
            return back()->with('success', 'User unsuspended and ticket resolved.');
        }

        $ticket->update(['status' => 'closed', 'admin_response' => 'Request denied or closed.']);
        return back()->with('success', 'Ticket closed.');
    }

    // Withdrawal Management
    public function withdrawals()
    {
        $withdrawals = Withdrawal::with('user')
            ->latest()
            ->paginate(15);
        
        return view('admin.withdrawals', compact('withdrawals'));
    }

    public function processWithdrawal(Request $request, Withdrawal $withdrawal)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'This withdrawal has already been processed.');
        }

        $user = $withdrawal->user;

        if ($request->action === 'approve') {
            // Check if user still has enough balance
            if ($user->balance < $withdrawal->amount) {
                return back()->with('error', 'User no longer has sufficient balance.');
            }

            // Deduct balance
            $user->balance -= $withdrawal->amount;
            $user->save();

            $withdrawal->update([
                'status' => 'approved',
                'notes' => $request->notes ?? 'Approved by admin.',
                'processed_at' => now(),
            ]);

            return back()->with('success', 'Withdrawal approved and balance deducted.');
        } else {
            $withdrawal->update([
                'status' => 'rejected',
                'notes' => $request->notes ?? 'Rejected by admin.',
                'processed_at' => now(),
            ]);

            return back()->with('success', 'Withdrawal rejected.');
        }
    }
}

