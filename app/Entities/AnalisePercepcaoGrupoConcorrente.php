<?php

namespace App\Entities;

class AnalisePercepcaoGrupoConcorrente extends AbstractEntity
{
	protected $datamap = [];

	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	
	protected $casts   = [
        'id' => 'int',
        'id_grupo' => 'int',
        'id_concorrente' => 'int',
    ];
}
