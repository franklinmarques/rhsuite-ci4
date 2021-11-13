<?php

namespace App\Entities;

class Pdi extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_usuario' => 'int',
        'nome' => 'string',
        'descricao' => '?string',
        'data_inicio' => '?date',
        'data_termino' => '?date',
        'observacao' => '?string',
        'status' => '?string',
    ];
}
