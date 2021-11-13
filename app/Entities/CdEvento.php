<?php

namespace App\Entities;

class CdEvento extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'codigo' => 'string',
        'nome' => 'string',
        'id_empresa' => 'int',
    ];
}
