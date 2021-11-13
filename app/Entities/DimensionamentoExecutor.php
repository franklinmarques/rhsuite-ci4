<?php

namespace App\Entities;

class DimensionamentoExecutor extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_crono_analise' => 'int',
        'tipo' => 'string',
        'id_equipe' => '?int',
        'id_usuario' => '?int',
        'status_apontamento_ativo' => '?bool',
        'tipo_apuracao' => '?string',
        'nivel_apuracao' => '?bool',
        'id_processo_apuracao' => '?int',
        'id_atividade_apuracao' => '?int',
        'id_etapa_apuracao' => '?int',
        'data_inicio_apuracao' => '?date',
        'data_termino_apuracao' => '?date',
    ];
}
