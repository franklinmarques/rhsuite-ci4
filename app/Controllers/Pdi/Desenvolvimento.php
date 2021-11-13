<?php

namespace App\Controllers\Pdi;

use App\Controllers\BaseController;

class Desenvolvimento extends BaseController
{
    public function index(): string
    {
        return view('pdi/desenvolvimento');
    }
}
