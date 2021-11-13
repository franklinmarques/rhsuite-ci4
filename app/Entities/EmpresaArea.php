<?php

namespace App\Entities;

class EmpresaArea extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_departamento' => '?int',
        'nome' => 'string',
    ];
}
