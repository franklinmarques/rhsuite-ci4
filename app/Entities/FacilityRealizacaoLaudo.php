<?php

namespace App\Entities;

class FacilityRealizacaoLaudo extends AbstractEntity
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
        'id_item' => 'int',
        'arquivo' => 'string',
        'tipo_mime' => 'string',
        'data_cadastro' => 'datetime',
        'local_armazem' => '?string',
        'sala_box' => '?string',
        'arquivo_fisico' => '?string',
        'pasta_caixa' => '?string',
        'codigo_localizador' => '?string',
    ];
}
