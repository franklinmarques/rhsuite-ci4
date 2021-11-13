<?php

namespace App\Controllers\Documentos;

use App\Controllers\BaseController;

class Modelos extends BaseController
{
    public function index(): string
    {
        return view('documentos/modelos');
    }
}
