<?php

namespace App\Entities;

class EmpresaSetor extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_area' => '?int',
        'nome' => 'string',
        'cnpj' => '?string',
    ];
}
