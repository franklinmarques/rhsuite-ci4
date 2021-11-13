<?php

namespace App\Entities;

class DocumentoTipo extends AbstractEntity
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
        'data_cadastro' => 'datetime',
        'descricao' => 'string',
        'categoria' => '?int',
    ];
}
