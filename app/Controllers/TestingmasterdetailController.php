<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class TestingmasterdetailController extends BaseController
{
    public function index(): string
    {
        return view('testingmasterdetail/index');
    }
}