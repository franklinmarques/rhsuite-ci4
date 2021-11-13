<?php

namespace App\Entities;

class UsuarioDemissao extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_usuario' => 'int',
        'id_empresa' => 'int',
        'data_demissao' => 'date',
        'motivo_demissao' => 'int',
        'observacoes' => '?string',
    ];
}
