<?php

namespace App\Controllers\Analises\Efe;

use App\Controllers\BaseController;
use App\Entities\AnaliseEfeAmbiente;
use App\Models\AnaliseEfeAmbienteModel;

class Ambientes extends BaseController
{
	public function index()
	{
        $efe = (new AnaliseEfeAmbienteModel)->find(7);
        $buu = $efe->raw()->resultado;
        dd($buu);
        return $this->response->setJSON($efe);
	}

    public function update()
    {
        $data = [
  "id_analise" => '1',
  "status" => 0,
  "risco_oportunidade" => "Risco 1",
  "peso" => 10,
  "impacto" => 3,
  "probabilidade_ocorrencia" => 90,
  "resultado" => "27,01",
  "created_at" => null,
  "updated_at" => "07/09/2021 10:08:37",
        ];
        $efe = new AnaliseEfeAmbiente($data);
        $efeModel = new AnaliseEfeAmbienteModel();
        return $efeModel->save($efe);
    }
}
