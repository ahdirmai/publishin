<?php

namespace App\Http\Controllers;

use App\Models\Waitlist;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MarketingController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Marketing/Index');
    }

    public function waitlist(): Response
    {
        $count = Waitlist::count() + 1247;
        return Inertia::render('Marketing/Waitlist', compact('count'));
    }

    public function privacy(): Response
    {
        return Inertia::render('Marketing/Privacy');
    }

    public function terms(): Response
    {
        return Inertia::render('Marketing/Terms');
    }

    public function storeWaitlist(Request $request)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'unique:waitlists,email'],
            'role'      => ['nullable', 'string', 'max:100'],
            'platforms' => ['nullable', 'array'],
        ]);

        Waitlist::create($data);

        return back()->with('waitlist_success', true);
    }
}
