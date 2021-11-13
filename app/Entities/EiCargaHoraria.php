<?php

namespace App\Entities;

class EiCargaHoraria extends AbstractEntity
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
        'data' => 'date',
        'horario_entrada' => '?time',
        'horario_saida' => '?time',
        'horario_entrada_1' => '?time',
        'horario_saida_1' => '?time',
        'total' => '?time',
        'carga_horaria' => '?time',
        'saldo_dia' => '?time',
        'observacoes' => '?string',
    ];
}
