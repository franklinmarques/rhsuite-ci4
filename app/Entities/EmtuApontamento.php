<?php

namespace App\Entities;

class EmtuApontamento extends AbstractEntity
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
        'data' => 'date',
        'horario_entrada' => '?datetime',
        'horario_intervalo' => '?datetime',
        'horario_retorno' => '?datetime',
        'horario_saida' => '?datetime',
        'qtde_dias' => '?int',
        'hora_atraso' => '?time',
        'hora_extra' => '?time',
        'desconto_folha' => '?time',
        'saldo_banco_horas' => '?time',
        'hora_glosa' => '?time',
        'observacoes' => '?string',
        'status' => 'string',
        'id_alocado_bck' => '?int',
    ];
}
