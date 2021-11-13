<?php

namespace App\Entities;

class EiAlocado extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_alocacao_escola' => 'int',
        'id_os_profissional' => '?int',
        'id_cuidador' => '?int',
        'cuidador' => '?string',
        'valor_hora' => '?decimal',
        'valor_hora_operacional' => '?decimal',
        'valor_hora_pagamento' => '?decimal',
        'horas_diarias' => '?decimal',
        'horas_semanais' => '?decimal',
        'qtde_dias' => '?decimal',
        'horas_semestre' => '?decimal',
        'total_dias_letivos' => 'int',
        'data_inicio_contrato' => '?date',
        'data_termino_contrato' => '?date',
        'horas_mensais_custo' => '?time',
        'valor_total' => '?decimal',
    ];
}
