<?php

namespace App\Entities;

class CandidatoVaga extends AbstractEntity
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
        'codigo_vaga' => 'int',
        'data_cadastro' => 'datetime',
        'status' => '?string',
    ];
}
