<?php

namespace App\Entities;

class IcomSpOperadora extends AbstractEntity
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
        'nome_operadora' => 'string',
        'link_acesso' => '?string',
    ];
}
