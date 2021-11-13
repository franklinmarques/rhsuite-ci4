<?php

namespace App\Entities;

class IcomSpComunicado extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_subcategoria' => 'int',
        'data' => 'date',
        'numero' => 'string',
        'tipo' => '?string',
        'titulo' => 'string',
        'conteudo' => 'string',
        'arquivo' => '?string',
        'palavras_chave' => '?string',
        'ativo' => '?bool',
    ];
}
