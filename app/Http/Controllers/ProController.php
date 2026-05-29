<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProController extends Controller
{
    /**
     * Show the GearGuard Pro pitch and benefits page.
     */
    public function index()
    {
        return view('pro.index');
    }

    /**
     * Handle the mock subscription payment and upgrade the user to Pro.
     */
    public function subscribe(Request $request)
    {
        $user = auth()->user();

        // MOCK BYPASS: Instantly upgrade the user for testing purposes
        $user->update(['is_pro' => true]);

        return redirect()->route('dashboard')
            ->with('success', 'Welcome to GearGuard Pro! You now get a flat 15% discount on all rentals, zero security deposits, and priority support.');
    }
}
