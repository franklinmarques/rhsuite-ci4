<?php

namespace App\Entities;

class PesquisaModelo extends AbstractEntity
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
        'observacoes' => '?string',
        'instrucoes' => '?string',
        'exclusao_bloqueada' => 'bool',
    ];
}
