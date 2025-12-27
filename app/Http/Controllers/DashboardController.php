<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.users');
        } elseif ($user->isSeller()) {
            return redirect()->route('seller.products.index');
        }

        // Buyer stays on home or goes to a specific buyer dashboard
        // For now, let's redirect buyer to home with a success message or just show balance
        return redirect()->route('home');
    }
}
