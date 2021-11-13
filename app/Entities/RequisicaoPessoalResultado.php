<?php

namespace App\Entities;

class RequisicaoPessoalResultado extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_teste' => 'int',
        'id_pergunta' => 'int',
        'peso_max' => '?int',
        'id_alternativa' => '?int',
        'valor' => '?int',
        'resposta' => '?string',
        'nota' => '?int',
        'data_avaliacao' => '?datetime',
    ];
}
