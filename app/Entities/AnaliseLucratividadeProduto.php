<?php

namespace App\Entities;

class AnaliseLucratividadeProduto extends AbstractEntity
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
        'categoria' => 'string',
        'nivel' => 'int',
        'potencial_valor' => '?decimal',
    ];
}
