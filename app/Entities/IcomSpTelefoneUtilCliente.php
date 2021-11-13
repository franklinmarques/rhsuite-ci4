<?php

namespace App\Entities;

class IcomSpTelefoneUtilCliente extends AbstractEntity
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
        'id_categoria' => 'int',
        'nome' => 'string',
        'id_estado' => '?int',
        'id_cidade' => '?int',
        'observacoes' => '?string',
    ];
}
