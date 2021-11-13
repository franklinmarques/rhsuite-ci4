<?php

namespace App\Controllers\Job_descriptor;

use App\Controllers\BaseController;

class Relatorio extends BaseController
{
    public function index(): string
    {
        return view('job_descriptor/relatorio');
    }
}
