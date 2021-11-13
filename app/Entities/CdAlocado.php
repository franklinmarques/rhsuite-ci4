<?php

namespace App\Entities;

class CdAlocado extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_alocacao' => 'int',
        'id_vinculado' => '?int',
        'cuidador' => '?string',
        'escola' => '?string',
        'municipio' => '?string',
        'supervisor' => '?string',
        'turno' => '?string',
        'dia_inicial' => '?int',
        'dia_limite' => '?int',
        'remanejado' => '?int',
    ];
}
