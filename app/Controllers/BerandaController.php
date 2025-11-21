<?php

namespace App\Controllers;

use App\Controllers\BaseController;


class BerandaController extends BaseController
{
    public function index(): string
    {
        return view('beranda');
    }

    public function about(): string
    {
        return view('about');
    }

    public function contact(): string
    {
        return view('contact');
    }

}
