<?php

namespace App\Entities;

class EiCurso extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_empresa' => 'int',
        'id_diretoria' => 'int',
        'nome' => 'string',
        'qtde_semestres' => '?bool',
    ];
}
