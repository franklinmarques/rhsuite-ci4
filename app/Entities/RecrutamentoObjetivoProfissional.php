<?php

namespace App\Entities;

class RecrutamentoObjetivoProfissional extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_usuario' => 'int',
        'objetivos' => 'string',
        'areas_interesse' => 'string',
        'pretensao_salarial' => '?decimal',
    ];
}
