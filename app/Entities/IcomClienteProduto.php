<?php

namespace App\Entities;

class IcomClienteProduto extends AbstractEntity
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
        'id_produto' => 'int',
        'valor_faturamento' => 'decimal',
        'valor_pagamento' => 'decimal',
    ];
}
