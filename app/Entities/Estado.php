<?php

namespace App\Entities;

class Estado extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'cod_uf' => 'int',
        'estado' => 'string',
        'uf' => 'string',
        'cod_capital' => '?int',
    ];
}
