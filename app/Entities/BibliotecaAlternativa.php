<?php

namespace App\Entities;

class BibliotecaAlternativa extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_questao' => 'int',
        'alternativa' => 'string',
        'peso' => 'int',
    ];
}
