<?php

namespace App\Entities;

class IcomAlocacao extends AbstractEntity
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
        'id_depto' => 'int',
        'id_area' => 'int',
        'id_setor' => 'int',
        'mes' => 'bool',
        'ano' => 'int',
        'data_aprovacao_pagto' => '?datetime',
        'id_usuario_aprovador_pagto' => '?string',
    ];
}
