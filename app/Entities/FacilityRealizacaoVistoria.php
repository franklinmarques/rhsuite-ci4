<?php

namespace App\Entities;

class FacilityRealizacaoVistoria extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_realizacao' => 'int',
        'id_modelo_vistoria' => 'int',
        'numero_os' => 'string',
        'possui_problema' => '?bool',
        'vistoriado' => '?bool',
        'nao_aplicavel' => 'bool',
        'descricao_problema' => '?string',
        'observacoes' => '?string',
        'data_abertura' => '?date',
        'data_realizacao' => '?date',
        'realizacao_cat' => '?string',
        'status' => '?string',
    ];
}
