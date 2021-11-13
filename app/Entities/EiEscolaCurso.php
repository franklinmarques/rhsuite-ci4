<?php

namespace App\Entities;

class EiEscolaCurso extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_escola' => 'int',
        'id_curso' => 'int',
        'id_diretoria_curso' => '?int',
    ];
}
