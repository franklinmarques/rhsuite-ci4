<?php

namespace App\Entities;

class EiLogDesalocacao extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'data' => 'datetime',
        'id_usuario' => 'int',
        'nome_usuario' => 'string',
        'operacao' => 'string',
        'nome_escola' => 'string',
        'opcao' => 'string',
        'id_alocado' => 'int',
        'nome_cuidador' => 'string',
        'periodo' => 'string',
    ];
}
