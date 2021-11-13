<?php

namespace App\Entities;

class AvaliacaoExpPergunta extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_modelo' => 'int',
        'pergunta' => 'string',
        'tipo' => 'string',
    ];
}
