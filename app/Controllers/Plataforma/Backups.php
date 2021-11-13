<?php

namespace App\Controllers\Plataforma;

use App\Controllers\BaseController;

class Backups extends BaseController
{
    public function index(): string
    {
        return view('plataforma/backups');
    }
}
