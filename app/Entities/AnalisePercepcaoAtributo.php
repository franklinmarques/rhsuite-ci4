<?php

namespace App\Entities;

class AnalisePercepcaoAtributo extends AbstractEntity
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
        'nome' => 'string',
        'descricao' => '?string',
        'descritivo_superior' => '?string',
        'descritivo_inferior' => '?string',
        'media_pontuacao' => '?float',
    ];
}
