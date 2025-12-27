<?php

namespace App\Http\Controllers;

use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawalController extends Controller
{
    public function index()
    {
        $withdrawals = Withdrawal::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        $user = Auth::user();

        return view('seller.withdrawals.index', compact('withdrawals', 'user'));
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->balance <= 0) {
            return redirect()->route('seller.withdrawals.index')
                ->with('error', 'You have no balance to withdraw.');
        }

        return view('seller.withdrawals.create', compact('user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $user->balance,
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_holder' => 'required|string|max:100',
        ]);

        // Check for pending withdrawal
        $hasPending = Withdrawal::where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return back()->with('error', 'You already have a pending withdrawal request.');
        }

        Withdrawal::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_holder' => $request->account_holder,
            'status' => 'pending',
        ]);

        return redirect()->route('seller.withdrawals.index')
            ->with('success', 'Withdrawal request submitted successfully!');
    }
}
