<?php

namespace App\Entities;

class EiMatriculado extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_alocacao_escola' => 'int',
        'id_os_aluno' => '?int',
        'id_aluno' => '?int',
        'aluno' => 'string',
        'id_aluno_curso' => '?int',
        'id_curso' => '?int',
        'curso' => '?string',
        'id_disciplina' => '?int',
        'disciplina' => '?string',
        'hipotese_diagnostica' => '?string',
        'modulo' => 'string',
        'status' => 'string',
        'data_inicio' => '?date',
        'data_termino' => '?date',
        'data_recesso' => '?date',
        'media_semestral' => '?decimal',
    ];
}
