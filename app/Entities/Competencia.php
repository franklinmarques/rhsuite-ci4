<?php

namespace App\Entities;

class Competencia extends AbstractEntity
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
        'id_cargo' => 'int',
        'descricao' => 'string',
        'data_inicio' => 'datetime',
        'data_termino' => 'datetime',
        'status' => 'int',
    ];
}
