<?php

namespace App\Entities;

class StContrato extends AbstractEntity
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
        'id_usuario' => '?int',
        'nome' => 'string',
        'depto' => 'string',
        'area' => 'string',
        'contrato' => 'string',
        'data_assinatura' => '?date',
    ];
}
