<?php

namespace App\Entities;

class Abcbr304Processo extends AbstractEntity
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
        'id_menu' => '?int',
        'url_pagina' => 'string',
        'orientacoes_gerais' => 'string',
        'nome_processo_1' => '?string',
        'nome_processo_2' => '?string',
        'arquivo_processo_1' => '?string',
        'arquivo_processo_2' => '?string',
        'nome_documentacao_1' => '?string',
        'nome_documentacao_2' => '?string',
        'arquivo_documentacao_1' => '?string',
        'arquivo_documentacao_2' => '?string',
    ];
}
