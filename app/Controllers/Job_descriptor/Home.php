<?php

namespace App\Controllers\Job_descriptor;

use App\Controllers\BaseController;

class Home extends BaseController
{
    public function index(): string
    {
        return view('job_descriptor/home');
    }
}
