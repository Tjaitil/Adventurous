<?php

namespace App\Http\Controllers;

class LandingController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        return view('landing');
    }
}
