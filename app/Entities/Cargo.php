<?php

namespace App\Entities;

class Cargo extends AbstractEntity
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
        'cargo' => 'string',
        'funcao' => 'string',
        'peso_competencias_tecnicas' => 'int',
        'peso_competencias_comportamentais' => 'int',
    ];
}
