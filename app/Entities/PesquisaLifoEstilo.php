<?php

namespace App\Entities;

class PesquisaLifoEstilo extends AbstractEntity
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
        'indice_resposta' => 'int',
        'estilo_personalidade_majoritario' => '?string',
        'estilo_personalidade_secundario' => '?string',
    ];
}
