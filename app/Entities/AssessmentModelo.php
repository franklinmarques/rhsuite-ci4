<?php

namespace App\Entities;

class AssessmentModelo extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'nome' => 'string',
        'id_empresa' => 'int',
        'tipo' => 'string',
        'observacoes' => '?string',
        'instrucoes' => '?string',
        'aleatorizacao' => '?string',
    ];
}
