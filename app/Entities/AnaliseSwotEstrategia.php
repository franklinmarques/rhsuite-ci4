<?php

namespace App\Entities;

class AnaliseSwotEstrategia extends AbstractEntity
{
	protected $datamap = [];

	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	
	protected $casts   = [
        'id' => 'int',
        'id_consolidacao' => 'int',
        'estrategia' => 'string',
    ];
}
