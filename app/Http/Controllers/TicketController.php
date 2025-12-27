<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function showSuspended()
    {
        // Check if user has an open unban request
        $existingTicket = Ticket::where('user_id', Auth::id())
                                ->where('type', 'unban_request')
                                ->where('status', 'open')
                                ->first();

        return view('suspended', compact('existingTicket'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        Ticket::create([
            'user_id' => Auth::id(),
            'type' => 'unban_request',
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'open',
        ]);

        return back()->with('success', 'Ticket submitted. Please wait for admin review.');
    }
}
