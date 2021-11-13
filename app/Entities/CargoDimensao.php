<?php

namespace App\Entities;

class CargoDimensao extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'nome' => 'string',
        'cargo_competencia' => 'int',
        'nivel' => 'int',
        'peso' => 'int',
        'atitude' => 'int',
        'id_dimensao' => '?int',
    ];
}
