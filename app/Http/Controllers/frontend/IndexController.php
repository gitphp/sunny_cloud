<?php

namespace App\Http\Controllers\frontend;

use Illuminate\View\View;

class IndexController extends AbstractController
{
    public function index(): View
    {
        return view('frontend.app');
    }
}
