<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class ParameterController extends BaseController
{
    public function index(): string
    {
        return view('parameter/index');
    }
}