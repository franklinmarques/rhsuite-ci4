<?php

namespace App\Entities;

class AssessmentPergunta extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_modelo' => 'int',
        'pergunta' => 'string',
        'tipo_resposta' => 'string',
        'tipo_eneagrama' => '?int',
        'id_competencia' => '?int',
        'competencia' => '?string',
        'justificativa' => '?int',
        'valor_min' => '?int',
        'valor_max' => '?int',
    ];
}
