<?php

namespace App\Controllers\Empresas;

use App\Controllers\BaseController;

class Home extends BaseController
{
    public function index(): string
    {
        return view('empresas/home');
    }
}
