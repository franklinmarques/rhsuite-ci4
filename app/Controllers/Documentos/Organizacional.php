<?php

namespace App\Controllers\Documentos;

use App\Controllers\BaseController;

class Organizacional extends BaseController
{
    public function index(): string
    {
        return view('documentos/organizacional');
    }
}
