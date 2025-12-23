<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class UserController extends BaseController
{
    public function index(): string
    {
        return view('user/index');
    }
}
