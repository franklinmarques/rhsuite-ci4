<?php

namespace App\Entities;

class EiAlocadoHorario extends AbstractEntity
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
        'id_os_horario' => '?int',
        'dia_semana' => 'int',
        'periodo' => '?bool',
        'cargo' => '?string',
        'cargo_mes2' => '?string',
        'cargo_mes3' => '?string',
        'cargo_mes4' => '?string',
        'cargo_mes5' => '?string',
        'cargo_mes6' => '?string',
        'cargo_mes7' => '?string',
        'funcao' => '?string',
        'funcao_mes2' => '?string',
        'funcao_mes3' => '?string',
        'funcao_mes4' => '?string',
        'funcao_mes5' => '?string',
        'funcao_mes6' => '?string',
        'funcao_mes7' => '?string',
        'horario_inicio_mes1' => '?time',
        'horario_inicio_mes2' => '?time',
        'horario_inicio_mes3' => '?time',
        'horario_inicio_mes4' => '?time',
        'horario_inicio_mes5' => '?time',
        'horario_inicio_mes6' => '?time',
        'horario_inicio_mes7' => '?time',
        'horario_termino_mes1' => '?time',
        'horario_termino_mes2' => '?time',
        'horario_termino_mes3' => '?time',
        'horario_termino_mes4' => '?time',
        'horario_termino_mes5' => '?time',
        'horario_termino_mes6' => '?time',
        'horario_termino_mes7' => '?time',
        'total_horas_mes1' => '?time',
        'total_horas_mes2' => '?time',
        'total_horas_mes3' => '?time',
        'total_horas_mes4' => '?time',
        'total_horas_mes5' => '?time',
        'total_horas_mes6' => '?time',
        'total_horas_mes7' => '?time',
        'total_semanas_mes1' => 'int',
        'total_semanas_mes2' => 'int',
        'total_semanas_mes3' => 'int',
        'total_semanas_mes4' => 'int',
        'total_semanas_mes5' => 'int',
        'total_semanas_mes6' => 'int',
        'total_semanas_mes7' => 'int',
        'total_semanas_sub1' => '?int',
        'total_semanas_sub2' => '?int',
        'desconto_mes1' => '?decimal',
        'desconto_mes2' => '?decimal',
        'desconto_mes3' => '?decimal',
        'desconto_mes4' => '?decimal',
        'desconto_mes5' => '?decimal',
        'desconto_mes6' => '?decimal',
        'desconto_mes7' => '?decimal',
        'desconto_sub1' => '?decimal',
        'desconto_sub2' => '?decimal',
        'endosso_mes1' => '?decimal',
        'endosso_mes2' => '?decimal',
        'endosso_mes3' => '?decimal',
        'endosso_mes4' => '?decimal',
        'endosso_mes5' => '?decimal',
        'endosso_mes6' => '?decimal',
        'endosso_mes7' => '?decimal',
        'endosso_sub1' => '?decimal',
        'endosso_sub2' => '?decimal',
        'total_mes1' => '?time',
        'total_mes2' => '?time',
        'total_mes3' => '?time',
        'total_mes4' => '?time',
        'total_mes5' => '?time',
        'total_mes6' => '?time',
        'total_mes7' => '?time',
        'total_sub1' => '?time',
        'total_sub2' => '?time',
        'total_endossado_mes1' => '?time',
        'total_endossado_mes2' => '?time',
        'total_endossado_mes3' => '?time',
        'total_endossado_mes4' => '?time',
        'total_endossado_mes5' => '?time',
        'total_endossado_mes6' => '?time',
        'total_endossado_mes7' => '?time',
        'total_endossado_sub1' => '?time',
        'total_endossado_sub2' => '?time',
        'id_cuidador_sub1' => '?int',
        'cargo_sub1' => '?string',
        'funcao_sub1' => '?string',
        'data_substituicao1' => '?date',
        'id_cuidador_sub2' => '?int',
        'cargo_sub2' => '?string',
        'funcao_sub2' => '?string',
        'data_substituicao2' => '?date',
        'data_inicio_contrato' => '?date',
        'data_termino_contrato' => '?date',
        'valor_hora_operacional' => '?decimal',
        'horas_mensais_custo' => '?time',
        'valor_hora_funcao' => '?decimal',
        'data_inicio_real' => '?date',
        'data_termino_real' => '?date',
    ];
}
