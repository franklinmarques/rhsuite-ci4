<?php

namespace App\Entities;

class RecrutamentoDocumentacao extends AbstractEntity
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
        'tipo' => 'string',
        'arquivo' => 'string',
    ];
}
