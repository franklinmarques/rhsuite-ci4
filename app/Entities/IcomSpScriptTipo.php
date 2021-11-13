<?php

namespace App\Entities;

class IcomSpScriptTipo extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_cliente' => 'int',
        'nome' => 'string',
    ];
}
