<?php

namespace App\Entities;

class AnalisePercepcao extends AbstractEntity
{
	protected $datamap = [];

	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
    
	protected $casts   = [
        'id' => 'int',
        'id_empresa' => 'int',
        'nome' => 'string',
        'data' => 'date',
        'tipo' => 'string',
        'descricao' => '?string',
    ];
}
