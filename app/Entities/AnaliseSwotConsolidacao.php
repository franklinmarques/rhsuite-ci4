<?php

namespace App\Entities;

class AnaliseSwotConsolidacao extends AbstractEntity
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
        'id_ambiente_externo' => 'int',
        'id_ambiente_interno' => 'int',
        'avaliacao' => 'string',
    ];
}
