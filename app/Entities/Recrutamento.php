<?php

namespace App\Entities;

class Recrutamento extends AbstractEntity
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
        'data_inicio' => 'datetime',
        'data_termino' => 'datetime',
        'requisitante' => 'string',
        'tipo_vaga' => '?string',
        'status' => '?string',
    ];
}
