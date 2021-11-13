<?php

namespace App\Entities;

class CandidatoFormacao extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_candidato' => 'int',
        'id_escolaridade' => 'int',
        'curso' => '?string',
        'tipo' => '?string',
        'instituicao' => 'string',
        'ano_conclusao' => '?int',
        'concluido' => 'bool',
    ];
}
