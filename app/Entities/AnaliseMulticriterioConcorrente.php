<?php

namespace App\Entities;

class AnaliseMulticriterioConcorrente extends AbstractEntity
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
        'nome' => 'string',
    ];
}
