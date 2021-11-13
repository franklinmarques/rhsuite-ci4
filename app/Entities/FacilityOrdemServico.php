<?php

namespace App\Entities;

class FacilityOrdemServico extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'numero_os' => 'int',
        'id_usuario' => '?int',
        'data_abertura' => 'date',
        'data_resolucao_problema' => '?date',
        'data_tratamento' => '?date',
        'data_fechamento' => '?date',
        'status' => 'string',
        'prioridade' => 'int',
        'id_requisitante' => 'int',
        'id_depto' => '?int',
        'id_area' => '?int',
        'id_setor' => '?int',
        'descricao_problema' => '?string',
        'descricao_solicitacao' => '?string',
        'complemento' => '?string',
        'observacoes' => '?string',
        'arquivo' => '?string',
        'resolucao_satisfatoria' => '?string',
        'observacoes_positivas' => '?string',
        'observacoes_negativas' => '?string',
    ];
}
