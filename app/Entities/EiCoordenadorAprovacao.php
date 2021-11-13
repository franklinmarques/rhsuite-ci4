<?php

namespace App\Entities;

class EiCoordenadorAprovacao extends AbstractEntity
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
        'id_aprovador' => 'int',
        'ano_referencia' => 'int',
        'semestre_referencia' => 'bool',
        'mes_referencia' => 'int',
        'cliente_aprovacao' => '?string',
        'depto_aprovacao' => '?string',
        'cargo_aprovacao' => '?string',
        'data_liberacao' => 'date',
        'arquivo_assinatura_aprovacao' => '?string',
    ];
}
