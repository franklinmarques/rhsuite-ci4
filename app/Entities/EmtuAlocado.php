<?php

namespace App\Entities;

class EmtuAlocado extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_alocacao' => 'int',
        'id_usuario' => '?int',
        'nome_usuario' => 'string',
        'id_funcao' => '?int',
    ];
}
