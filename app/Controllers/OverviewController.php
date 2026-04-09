<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class OverviewController extends BaseController
{
    public function transaksi()
    {
        return view('overview/transaksi');
    }

    public function master()
    {
        return view('overview/master');
    }
}
