<?php

namespace App\Controllers\Empresas;

use App\Controllers\BaseController;

class Estrutura_organizacional extends BaseController
{
    public function index(): string
    {
        return view('empresas/estrutura_organizacional');
    }
}
