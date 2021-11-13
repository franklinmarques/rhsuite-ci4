<?php

namespace App\Controllers\Vagas;

use App\Controllers\BaseController;

class Curriculo extends BaseController
{
    public function index(): string
    {
        return view('vagas/curriculo');
    }
}
