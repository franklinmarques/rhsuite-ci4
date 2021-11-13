<?php

namespace App\Entities;

class AvaliacaoExpModelo extends AbstractEntity
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
        'tipo' => 'string',
        'observacao' => '?string',
        'id_copia' => '?int',
    ];
}
