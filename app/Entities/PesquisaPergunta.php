<?php

namespace App\Entities;

class PesquisaPergunta extends AbstractEntity
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
        'id_categoria' => '?int',
        'pergunta' => 'string',
        'tipo_resposta' => 'string',
        'tipo_eneagrama' => '?int',
        'prefixo_resposta' => '?string',
        'justificativa' => '?int',
        'valor_min' => '?int',
        'valor_max' => '?int',
    ];
}
