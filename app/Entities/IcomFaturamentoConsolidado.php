<?php

namespace App\Entities;

class IcomFaturamentoConsolidado extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_aprovacao' => 'int',
        'id_cliente' => 'int',
        'total_horas' => '?decimal',
        'valor_total' => '?decimal',
        'data_validacao' => '?date',
        'data_nova_validacao' => '?date',
        'data_aprovacao' => '?date',
        'data_faturado' => '?date',
        'assinatura_validador' => '?string',
    ];
}
