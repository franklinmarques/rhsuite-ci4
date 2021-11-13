<?php

namespace App\Entities;

class StAlocado extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_alocacao' => 'int',
        'id_usuario' => 'int',
        'nome' => 'string',
        'cargo' => '?string',
        'funcao' => '?string',
        'id_posto' => '?int',
        'tipo_horario' => 'string',
        'nivel' => 'string',
        'tipo_bck' => '?string',
        'data_recesso' => '?date',
        'data_retorno' => '?date',
        'id_usuario_bck' => '?int',
        'nome_bck' => '?string',
        'data_desligamento' => '?date',
        'id_usuario_sub' => '?int',
        'nome_sub' => '?string',
        'dias_acrescidos' => '?decimal',
        'horas_acrescidas' => '?decimal',
        'total_acrescido' => '?decimal',
        'total_faltas' => '?time',
        'total_atrasos' => '?time',
        'horas_saldo' => '?time',
        'horas_saldo_acumulado' => '?time',
    ];
}
