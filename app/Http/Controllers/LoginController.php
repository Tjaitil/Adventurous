<?php

namespace App\Http\Controllers;

use App\Services\GameLogService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * @return View|Factory|RedirectResponse
     */
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('client');
        }

        return view('login')->with('title', 'Login');
    }

    /**
     * @return RedirectResponse
     */
    public function authenticate(Request $request)
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

    /**
     * @return Redirector|RedirectResponse
     */
    public function logOut(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
