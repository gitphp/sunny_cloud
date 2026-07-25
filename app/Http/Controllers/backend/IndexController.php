<?php

namespace App\Http\Controllers\backend;

use Illuminate\View\View;

class IndexController extends AbstractController
{
    public function index(): View
    {
        return view('backend.app');
    }
}
