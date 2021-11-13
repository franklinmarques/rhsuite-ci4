<?php

namespace App\Entities;

class EiMapaUnidade extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_alocacao' => 'int',
        'id_escola' => '?int',
        'escola' => 'string',
        'municipio' => 'string',
    ];
}
