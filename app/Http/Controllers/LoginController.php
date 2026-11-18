<?php

namespace App\Http\Controllers;

use App\Services\GameLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    public function index(): RedirectResponse|InertiaResponse
    {
        if (Auth::check()) {
            return redirect()->route('client');
        }

        return Inertia::render('LoginPage');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            GameLogService::addInfoLog('Welcome! Enjoy your stay!');

            return redirect()->intended(route('client'));
        }

        return back()
            ->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])
            ->onlyInput('email');

    }

    public function logOut(Request $request): Response
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return Inertia::location('/login');
    }
}
