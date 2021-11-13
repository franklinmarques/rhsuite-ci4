<?php

namespace App\Entities;

class DimensionamentoCronoAnalise extends AbstractEntity
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
        'nome' => 'string',
        'id_processo' => '?int',
        'data_inicio' => 'date',
        'data_termino' => 'date',
        'status' => '?string',
        'base_tempo' => '?string',
        'unidade_producao' => '?string',
        'data_inicio_apuracao' => '?date',
        'data_termino_apuracao' => '?date',
        'tipo_apuracao' => '?string',
        'nivel_apuracao' => '?bool',
        'id_processo_apuracao' => '?int',
        'id_atividade_apuracao' => '?int',
        'id_etapa_apuracao' => '?int',
        'status_apontamento' => '?bool',
    ];
}
