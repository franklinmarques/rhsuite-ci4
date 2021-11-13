<?php

namespace App\Controllers\Plataforma;

use App\Controllers\BaseController;

class Processos extends BaseController
{
    public function index(): string
    {
        return view('plataforma/processos');
    }
}
