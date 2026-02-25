<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class MenuController extends BaseController
{
    public function index(): string
    {
        return view('menu/index');
    }
}
