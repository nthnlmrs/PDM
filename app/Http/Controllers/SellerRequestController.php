<?php

namespace App\Http\Controllers;

use App\Models\SellerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerRequestController extends Controller
{
    public function create()
    {
        if (Auth::user()->role !== 'user') {
            return redirect()->route('home');
        }
        return view('seller.apply');
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'user') {
            return back()->with('error', 'Only users can apply.');
        }

        // Check if already has pending request
        $existing = SellerRequest::where('user_id', Auth::id())->where('status', 'pending')->first();
        if ($existing) {
            return back()->with('error', 'You already have a pending application.');
        }

        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_description' => 'required|string',
            'store_address' => 'required|string',
        ]);

        SellerRequest::create([
            'user_id' => Auth::id(),
            'status' => 'pending',
            'store_name' => $request->store_name,
            'store_description' => $request->store_description,
            'store_address' => $request->store_address,
        ]);

        return redirect()->route('home')->with('success', 'Application submitted. Waiting for Admin approval.');
    }
}
