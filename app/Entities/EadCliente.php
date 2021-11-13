<?php

namespace App\Entities;

class EadCliente extends AbstractEntity
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
        'cliente' => 'string',
        'email' => 'string',
        'senha' => 'string',
        'token' => 'string',
        'foto' => '?string',
        'data_cadastro' => 'datetime',
        'data_edicao' => '?datetime',
        'status' => 'int',
    ];
}
