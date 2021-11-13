<?php

namespace App\Entities;

class PesquisaAlternativa extends AbstractEntity
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
        'id_pergunta' => '?int',
        'alternativa' => 'string',
        'peso' => 'int',
    ];
}
