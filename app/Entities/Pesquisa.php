<?php

namespace App\Entities;

class Pesquisa extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'nome' => 'string',
        'id_modelo' => 'int',
        'data_inicio' => 'datetime',
        'data_termino' => 'datetime',
    ];
}
