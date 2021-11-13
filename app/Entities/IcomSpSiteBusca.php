<?php

namespace App\Entities;

class IcomSpSiteBusca extends AbstractEntity
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
        'link_acesso' => 'string',
        'observacoes' => '?string',
    ];
}
