<?php

namespace App\Entities;

class IcomEvento extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_alocado' => 'int',
        'id_old' => '?int',
        'tipo_entrada' => 'string',
        'entrada_automatica' => '?bool',
        'data_entrada' => 'date',
        'hora_entrada' => '?time',
        'horario_especial_entrada' => '?time',
        'desconto_folha_entrada' => '?time',
        'saldo_horas_entrada' => '?time',
        'tipo_saida' => '?string',
        'saida_automatica' => '?bool',
        'data_saida' => '?date',
        'hora_saida' => '?time',
        'horario_especial_saida' => '?time',
        'desconto_folha_saida' => '?time',
        'saldo_horas_saida' => '?time',
        'horas_diarias' => '?time',
        'minutos_folga' => '?time',
        'acrescimo_folha' => '?time',
        'observacoes' => '?string',
    ];
}
