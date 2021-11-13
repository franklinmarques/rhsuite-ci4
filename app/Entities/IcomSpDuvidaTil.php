<?php

namespace App\Entities;

class IcomSpDuvidaTil extends AbstractEntity
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
        'data' => 'date',
        'titulo' => 'string',
        'pergunta' => '?string',
        'resposta' => '?string',
        'palavras_chave' => '?string',
        'ativo' => '?bool',
    ];
}
