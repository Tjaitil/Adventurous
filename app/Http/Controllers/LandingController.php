<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class LandingController extends Controller
{
    /**
     * @return View|Factory
     */
    public function index()
    {
        return view('landing');
    }
}
