<?php

namespace App\Entities;

class ComportamentoDimensao extends AbstractEntity
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
        'nivel' => 'int',
        'peso' => 'double',
        'status' => 'int',
        'atitude' => 'int',
        'id_competencia' => 'int',
    ];
}
