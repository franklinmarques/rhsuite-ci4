<?php

namespace App\Entities;

class Municipio extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'cod_mun' => 'int',
        'cod_uf' => 'int',
        'municipio' => 'string',
    ];
}
