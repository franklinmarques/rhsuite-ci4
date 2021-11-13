<?php

namespace App\Entities;

class StPosto extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_usuario' => 'int',
        'data' => 'date',
        'depto' => '?string',
        'area' => '?string',
        'setor' => '?string',
        'cargo' => '?string',
        'funcao' => '?string',
        'contrato' => '?string',
        'total_dias_mensais' => 'int',
        'total_horas_diarias' => 'int',
        'matricula' => '?string',
        'login' => '?string',
        'horario_entrada' => '?time',
        'horario_saida' => '?time',
        'valor_posto' => 'decimal',
        'valor_dia' => 'decimal',
        'valor_hora' => 'decimal',
    ];
}
