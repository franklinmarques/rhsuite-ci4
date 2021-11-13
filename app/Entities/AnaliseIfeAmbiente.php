<?php

namespace App\Entities;

class AnaliseIfeAmbiente extends AbstractEntity
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
        'ponto_fraco_forte' => 'string',
        'peso' => '?int',
        'impacto' => '?int',
        'resultado' => '?int',
    ];
}
