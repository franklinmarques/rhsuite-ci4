<?php

namespace App\Entities;

class ComportamentoSugestao extends AbstractEntity
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
        'id_competencia_sugestao' => 'int',
    ];
}
