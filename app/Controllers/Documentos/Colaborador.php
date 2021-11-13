<?php

namespace App\Controllers\Documentos;

use App\Controllers\BaseController;

class Colaborador extends BaseController
{
    public function index(): string
    {
        return view('documentos/colaborador');
    }
}
