<?php

namespace App\Entities;

class EiOrdemServicoTurma extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_os_aluno' => 'int',
        'id_os_horario' => 'int',
    ];
}
