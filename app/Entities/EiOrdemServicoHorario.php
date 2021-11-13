<?php

namespace App\Entities;

class EiOrdemServicoHorario extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_os_profissional' => 'int',
        'id_funcao' => '?int',
        'id_os_profissional_sub1' => '?int',
        'id_funcao_sub1' => '?int',
        'data_substituicao1' => '?date',
        'id_os_profissional_sub2' => '?int',
        'id_funcao_sub2' => '?int',
        'data_substituicao2' => '?date',
        'dia_semana' => '?int',
        'periodo' => '?bool',
        'horario_inicio' => '?time',
        'horario_termino' => '?time',
        'total_dias_mes1' => '?int',
        'total_dias_mes2' => '?int',
        'total_dias_mes3' => '?int',
        'total_dias_mes4' => '?int',
        'total_dias_mes5' => '?int',
        'total_dias_mes6' => '?int',
        'valor_hora' => '?decimal',
        'horas_diarias' => '?decimal',
        'qtde_dias' => '?int',
        'horas_semanais' => '?decimal',
        'qtde_semanas' => '?int',
        'horas_mensais' => '?decimal',
        'horas_semestre' => '?decimal',
        'valor_hora_mensal' => '?decimal',
        'valor_hora_operacional' => '?decimal',
        'horas_mensais_custo' => '?time',
        'data_inicio_contrato' => '?date',
        'data_termino_contrato' => '?date',
        'desconto_mensal_1' => '?decimal',
        'desconto_mensal_2' => '?decimal',
        'desconto_mensal_3' => '?decimal',
        'desconto_mensal_4' => '?decimal',
        'desconto_mensal_5' => '?decimal',
        'desconto_mensal_6' => '?decimal',
        'valor_mensal_1' => '?decimal',
        'valor_mensal_2' => '?decimal',
        'valor_mensal_3' => '?decimal',
        'valor_mensal_4' => '?decimal',
        'valor_mensal_5' => '?decimal',
        'valor_mensal_6' => '?decimal',
    ];
}
