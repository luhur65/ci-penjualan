<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
    public function index()
    {
        $user = session()->get('user_data');
        return view('dashboard/index', [
            'user' => $user,
        ]);
    }
}
