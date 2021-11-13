<?php

namespace App\Entities;

class EiSaldoBancoHora extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_supervisao' => 'int',
        'saldo_mes1' => '?string',
        'saldo_mes2' => '?string',
        'saldo_mes3' => '?string',
        'saldo_mes4' => '?string',
        'saldo_mes5' => '?string',
        'saldo_mes6' => '?string',
        'saldo_mes7' => '?string',
        'saldo_acumulado_mes1' => '?string',
        'saldo_acumulado_mes2' => '?string',
        'saldo_acumulado_mes3' => '?string',
        'saldo_acumulado_mes4' => '?string',
        'saldo_acumulado_mes5' => '?string',
        'saldo_acumulado_mes6' => '?string',
        'saldo_acumulado_mes7' => '?string',
    ];
}
