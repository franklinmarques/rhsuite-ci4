<?php

namespace App\Entities;

class IcomModeloProposta extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_produto' => 'int',
        'nome' => 'string',
        'descricao_abertura' => '?string',
        'descricao_objeto' => '?string',
        'descricao_complemento' => '?string',
        'descricao_condicoes_pagamento' => '?string',
    ];
}
