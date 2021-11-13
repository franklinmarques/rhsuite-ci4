<?php

namespace App\Entities;

class DimensionamentoPrograma extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_job' => 'int',
        'id_executor' => 'int',
        'volume_trabalho' => '?decimal',
        'qtde_horas_disponiveis' => '?decimal',
        'tipo_valor' => '?string',
        'tipo_mao_obra' => '?string',
        'unidades' => '?string',
        'mao_obra' => '?string',
        'carga_horaria_necessaria' => '?decimal',
        'horario_inicio_projetado' => '?time',
        'horario_termino_projetado' => '?time',
        'horario_inicio_real' => '?time',
        'horario_termino_real' => '?time',
        'status' => 'string',
    ];
}
