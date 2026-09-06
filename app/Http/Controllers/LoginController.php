<?php

namespace App\Http\Controllers;

use App\Services\GameLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    public function index(): RedirectResponse|View
    {
        if (Auth::check()) {
            return redirect()->route('client');
        }

        return view('login')->with('title', 'Login');
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

            return redirect()->intended('client');
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
