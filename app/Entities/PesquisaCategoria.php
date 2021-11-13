<?php

namespace App\Entities;

class PesquisaCategoria extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_modelo' => 'int',
        'categoria' => 'string',
    ];
}
