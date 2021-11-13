<?php

namespace App\Entities;

class IcomPosto extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_setor' => 'int',
        'id_supervisor' => '?int',
        'id_usuario' => 'int',
        'id_funcao' => 'int',
        'categoria' => 'string',
        'matricula' => '?int',
        'endereco_ip1' => '?string',
        'endereco_ip2' => '?string',
        'valor_hora_mei' => '?decimal',
        'qtde_horas_mei' => '?time',
        'qtde_horas_dia_mei' => '?time',
        'valor_mes_clt' => '?decimal',
        'qtde_meses_clt' => '?time',
        'qtde_horas_dia_clt' => '?time',
        'dia_semana' => '?string',
        'horario_entrada' => '?time',
        'horario_intervalo' => '?time',
        'horario_retorno' => '?time',
        'horario_saida' => '?time',
        'horas_dia' => '?time',
        'minutos_descanso_dia' => '?time',
        'dia_semana_extra_1' => '?string',
        'horario_entrada_extra_1' => '?time',
        'horario_intervalo_extra_1' => '?time',
        'horario_retorno_extra_1' => '?time',
        'horario_saida_extra_1' => '?time',
        'horas_dia_extra_1' => '?time',
        'minutos_descanso_dia_extra_1' => '?time',
        'dia_semana_extra_2' => '?string',
        'horario_entrada_extra_2' => '?time',
        'horario_intervalo_extra_2' => '?time',
        'horario_retorno_extra_2' => '?time',
        'horario_saida_extra_2' => '?time',
        'horas_dia_extra_2' => '?time',
        'minutos_descanso_dia_extra_2' => '?time',
        'data_edicao' => '?date',
        'tipo_ultimo_evento' => '?string',
    ];
}
