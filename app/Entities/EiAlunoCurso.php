<?php

namespace App\Entities;

class EiAlunoCurso extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_aluno' => 'int',
        'id_curso' => 'int',
        'id_escola' => 'int',
        'qtde_semestre' => 'int',
        'semestre_inicial' => 'string',
        'semestre_final' => '?string',
        'nota_geral' => '?decimal',
        'status_ativo' => '?int',
    ];
}
