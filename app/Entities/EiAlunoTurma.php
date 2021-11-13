<?php

namespace App\Entities;

class EiAlunoTurma extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_semestre' => 'int',
        'id_disciplina' => 'int',
        'id_cuidador' => '?int',
        'dia_semana' => '?int',
        'hora_inicio' => '?time',
        'hora_termino' => '?time',
        'periodo' => '?string',
        'nota' => '?decimal',
    ];
}
