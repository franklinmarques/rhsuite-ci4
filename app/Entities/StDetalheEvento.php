<?php

namespace App\Entities;

class StDetalheEvento extends AbstractEntity
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
        'codigo' => 'string',
        'nome' => 'string',
    ];
}
