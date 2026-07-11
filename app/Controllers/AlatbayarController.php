<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class AlatbayarController extends BaseController
{
    public function index(): string
    {
        return view('alatbayar/index');
    }
}