<?php

namespace App\Controllers\Vagas;

use App\Controllers\BaseController;

class Home extends BaseController
{
    public function index(): string
    {
        return view('vagas/home');
    }
}
