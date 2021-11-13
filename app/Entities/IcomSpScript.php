<?php

namespace App\Entities;

class IcomSpScript extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_tipo' => 'int',
        'data' => 'date',
        'formato' => '?string',
        'titulo' => 'string',
        'conteudo' => '?string',
        'arquivo_video' => '?string',
        'arquivo_pdf' => '?string',
        'palavras_chave' => '?string',
        'ativo' => '?bool',
    ];
}
