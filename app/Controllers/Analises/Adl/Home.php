<?php

namespace App\Controllers\Analises\Adl;

use App\Controllers\BaseController;
use App\Models\AnaliseAdlModel;

class Home extends BaseController
{
    public function index()
    {
        $adl = (new AnaliseAdlModel())->first();
        return $this->response->setJSON($adl);
    }
}
