<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class IndexController extends Controller
{
    public function index(): View
    {
        return view('backend.app');
    }
}
