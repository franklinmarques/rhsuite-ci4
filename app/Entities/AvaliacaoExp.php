<?php

namespace App\Entities;

class AvaliacaoExp extends AbstractEntity
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
        'data_inicio' => 'date',
        'data_termino' => 'date',
        'ativo' => 'bool',
    ];
}
