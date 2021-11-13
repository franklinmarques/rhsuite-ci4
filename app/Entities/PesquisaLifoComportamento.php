<?php

namespace App\Entities;

class PesquisaLifoComportamento extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_estilo' => 'int',
        'situacao_comportamental' => 'string',
        'nome' => 'string',
    ];
}
