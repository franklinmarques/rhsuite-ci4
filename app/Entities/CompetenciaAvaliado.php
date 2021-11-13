<?php

namespace App\Entities;

class CompetenciaAvaliado extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_competencia' => 'int',
        'id_usuario' => 'int',
    ];
}
