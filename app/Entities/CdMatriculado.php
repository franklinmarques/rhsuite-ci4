<?php

namespace App\Entities;

class CdMatriculado extends AbstractEntity
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
        'id_aluno' => '?int',
        'aluno' => 'string',
        'escola' => 'string',
        'supervisor' => 'string',
        'hipotese_diagnostica' => 'string',
        'turno' => 'string',
        'status' => 'string',
        'dia_inicial' => '?int',
        'dia_limite' => '?int',
    ];
}
