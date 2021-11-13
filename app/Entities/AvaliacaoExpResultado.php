<?php

namespace App\Entities;

class AvaliacaoExpResultado extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_avaliador' => 'int',
        'id_pergunta' => 'int',
        'id_alternativa' => '?int',
        'resposta' => '?string',
        'data_avaliacao' => 'datetime',
    ];
}
