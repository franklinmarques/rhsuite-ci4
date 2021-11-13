<?php

namespace App\Entities;

class EadClienteResultado extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_acesso' => 'int',
        'id_questao' => 'int',
        'id_alternativa' => '?int',
        'valor' => '?int',
        'resposta' => '?string',
        'nota' => '?int',
        'data_avaliacao' => 'datetime',
        'status' => 'int',
    ];
}
