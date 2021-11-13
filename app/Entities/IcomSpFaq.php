<?php

namespace App\Entities;

class IcomSpFaq extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_cliente' => 'int',
        'data' => 'date',
        'formato' => '?string',
        'titulo' => 'string',
        'pergunta' => '?string',
        'resposta' => '?string',
        'arquivo_video' => '?string',
        'palavras_chave' => '?string',
        'ativo' => '?bool',
    ];
}
