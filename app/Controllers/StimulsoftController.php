<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class StimulsoftController extends BaseController
{
    public function viewer()
    {
        $data = [
            'title' => 'Stimulsoft Viewer'
        ];
        return view('stimulsoft/viewer', $data);
    }

    public function designer()
    {
        $data = [
            'title' => 'Stimulsoft Designer'
        ];
        return view('stimulsoft/designer', $data);
    }
}
