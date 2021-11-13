<?php

namespace App\Entities;

class Documento extends AbstractEntity
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
        'id_tipo' => '?int',
        'id_colaborador' => '?int',
        'data_cadastro' => 'datetime',
        'descricao' => 'string',
        'arquivo' => '?string',
        'observacoes' => '?string',
    ];
}
