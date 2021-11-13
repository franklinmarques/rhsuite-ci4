<?php

namespace App\Controllers\Plataforma;

use App\Controllers\BaseController;

class Acessos extends BaseController
{
    public function index(): string
    {
        return view('plataforma/acessos');
    }
}
