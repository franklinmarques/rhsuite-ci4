<?php

namespace App\Controllers\Pdi;

use App\Controllers\BaseController;

class Home extends BaseController
{
    public function index(): string
    {
        return view('pdi/home');
    }
}
