<?php

namespace App\Entities;

class PdiDesenvolvimento extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_pdi' => 'int',
        'competencia' => 'string',
        'descricao' => 'string',
        'expectativa' => 'string',
        'resultado' => 'string',
        'data_inicio' => 'datetime',
        'data_termino' => 'datetime',
        'status' => '?string',
    ];
}
