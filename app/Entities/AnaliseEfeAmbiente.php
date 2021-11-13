<?php

namespace App\Entities;

class AnaliseEfeAmbiente extends AbstractEntity
{
	protected $datamap = [];

	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
    
	protected $casts   = [
        'id' => 'int',
        'id_analise' => 'int',
        'status' => 'bool',
        'risco_oportunidade' => 'string',
        'peso' => '?int',
        'impacto' => '?int',
        'probabilidade_ocorrencia' => '?int',
        'resultado' => '?float',
    ];
}
