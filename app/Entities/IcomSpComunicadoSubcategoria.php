<?php

namespace App\Entities;

class IcomSpComunicadoSubcategoria extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_categoria' => 'int',
        'nome' => 'string',
    ];
}
