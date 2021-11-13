<?php

namespace App\Entities;

class IcomSpLembrete extends AbstractEntity
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
        'tipo' => 'string',
        'conteudo_texto' => '?string',
        'arquivo_video' => '?string',
        'palavras_chave' => '?string',
        'ativo' => '?bool',
    ];
}
