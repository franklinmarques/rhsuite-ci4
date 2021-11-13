<?php

namespace App\Controllers\Empresas;

use App\Controllers\BaseController;

class Cargos_funcoes extends BaseController
{
    public function index(): string
    {
        return view('empresas/cargos_funcoes');
    }
}
