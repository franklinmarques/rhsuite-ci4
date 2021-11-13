<?php

namespace App\Entities;

class AnaliseMulticriterioConcorrencia extends AbstractEntity
{
	protected $datamap = [];

	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	
	protected $casts   = [
        'id' => 'int',
        'id_criterio' => 'int',
        'id_concorrente' => 'int',
        'desempenho' => 'int',
        'resultado' => 'float',
    ];
}
