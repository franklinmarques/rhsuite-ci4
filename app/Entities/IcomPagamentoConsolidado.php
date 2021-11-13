<?php

namespace App\Entities;

class IcomPagamentoConsolidado extends AbstractEntity
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
        'id_usuario_prestador' => 'int',
        'total_horas' => '?decimal',
        'valor_total' => '?decimal',
        'data_validacao' => '?date',
        'assinatura_validador' => '?string',
    ];
}
