<?php

namespace App\Entities;

class PesquisaJungCaracteristica extends AbstractEntity
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
        'tipo_comportamental' => 'string',
        'nome' => 'string',
    ];
}
