<?php

namespace App\Controllers\Plataforma;

use App\Controllers\BaseController;

class Visitas extends BaseController
{
    public function index(): string
    {
        return view('plataforma/visitas');
    }
}
