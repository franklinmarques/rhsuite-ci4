<?php

namespace App\Entities;

class EiOrdemServicoAluno extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_ordem_servico_escola' => 'int',
        'id_aluno' => 'int',
        'id_aluno_curso' => 'int',
        'data_inicio' => '?date',
        'data_termino' => '?date',
        'modulo' => '?string',
        'nota' => '?decimal',
    ];
}
