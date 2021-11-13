<?php

namespace App\Entities;

class AnalisePercepcaoPontuacao extends AbstractEntity
{
	protected $datamap = [];

	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	
	protected $casts   = [
        'id' => 'int',
        'id_atributo' => 'int',
        'id_concorrente' => '?int',
        'id_grupo' => '?int',
        'pontuacao' => 'int',
    ];
}
