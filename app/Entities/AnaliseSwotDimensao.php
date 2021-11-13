<?php

namespace App\Entities;

class AnaliseSwotDimensao extends AbstractEntity
{
	protected $datamap = [];

	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	
	protected $casts   = [
        'id' => 'int',
        'id_analise' => 'int',
        'tipo_ambiente' => 'string',
        'avaliacao' => 'string',
    ];
}
