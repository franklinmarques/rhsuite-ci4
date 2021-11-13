<?php

namespace App\Entities;

class RelatorioGestao extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_empresa' => 'int',
        'id_usuario' => 'int',
        'id_depto' => '?int',
        'id_area' => '?int',
        'id_setor' => '?int',
        'mes_referencia' => 'bool',
        'ano_referencia' => 'int',
        'data_fechamento' => 'date',
        'indicadores' => '?string',
        'riscos_oportunidades' => '?string',
        'ocorrencias' => '?string',
        'necessidades_investimentos' => '?string',
        'objetivos_imediatos' => '?string',
        'objetivos_futuros' => '?string',
        'parecer_final' => '?string',
        'observacoes' => '?string',
        'status' => 'string',
    ];
}
