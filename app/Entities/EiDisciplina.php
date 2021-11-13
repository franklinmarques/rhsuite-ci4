<?php

namespace App\Entities;

class EiDisciplina extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_curso' => 'int',
        'nome' => 'string',
        'qtde_semestres' => '?bool',
    ];
}
