<?php

namespace App\Entities;

class EiMatriculadoTurma extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_matriculado' => 'int',
        'id_alocado_horario' => 'int',
    ];
}
