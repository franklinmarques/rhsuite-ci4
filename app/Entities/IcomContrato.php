<?php

namespace App\Entities;

class IcomContrato extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'codigo' => 'int',
        'id_empresa' => 'int',
        'codigo_proposta' => 'int',
        'tipo_contrato' => 'string',
        'centro_custo' => '?string',
        'condicoes_pagamento' => '?string',
        'data_vencimento' => 'date',
        'status_ativo' => 'bool',
        'arquivo' => '?string',
    ];
}
