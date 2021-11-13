<?php

namespace App\Entities;

class IcomSpContato extends AbstractEntity
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
        'data' => 'date',
        'nome_responsavel' => 'string',
        'nome_empresa' => 'string',
        'telefone' => '?string',
        'email' => 'string',
        'motivo_ligacao' => 'string',
        'agente_comercial' => '?string',
        'possui_interesse' => 'bool',
    ];
}
