<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class ErrorController extends BaseController
{
    public function index(): string
    {
        return view('error/index');
    }
}