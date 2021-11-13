<?php

namespace App\Entities;

class IcomFaturamentoAprovacao extends AbstractEntity
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
        'mes_referencia' => 'int',
        'ano_referencia' => 'int',
        'id_usuario_aprovador' => 'int',
        'data_aprovacao' => 'datetime',
    ];
}
