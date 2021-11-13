<?php

namespace App\Entities;

class RecrutamentoModelo extends AbstractEntity
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
        'nome' => 'string',
        'tipo' => 'string',
        'tipo_old' => 'string',
        'observacoes' => '?string',
        'instrucoes' => '?string',
        'aleatorizacao' => '?string',
    ];
}
