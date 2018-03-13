<?php

namespace Helori\LaravelBackend\Controllers;

use Illuminate\Http\Request;
use Helori\LaravelBackend\Controllers\Controller;


class FrontController extends Controller
{
    public function home()
    {
        return view('laravel-backend::app');
    }

}
