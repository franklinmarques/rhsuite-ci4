<?php

namespace App\Entities;

class IcomSpItem extends AbstractEntity
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
        'tipo_old' => '?int',
        'tipo' => '?string',
        'versao' => '?string',
        'mes' => 'int',
        'ano' => 'int',
        'descricao' => '?string',
        'arquivo' => 'string',
        'privado' => 'bool',
    ];
}
