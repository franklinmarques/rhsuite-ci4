<?php

namespace App\Entities;

class AvaliacaoExpAvaliador extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_avaliado' => 'int',
        'id_avaliador' => 'int',
        'data_avaliacao' => 'date',
        'id_evento' => '?int',
    ];
}
