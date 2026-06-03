<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller {
    public function create(): Response {
        return Inertia::render('Auth/Login');
    }

    public function store(Request $request): RedirectResponse {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Kredensial tidak cocok dengan data kami.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();
        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
