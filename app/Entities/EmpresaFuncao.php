<?php

namespace App\Entities;

class EmpresaFuncao extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_cargo' => 'int',
        'nome' => 'string',
        'ocupacao_cbo' => '?int',
    ];
}
