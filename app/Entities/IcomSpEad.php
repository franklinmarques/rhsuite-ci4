<?php

namespace App\Entities;

class IcomSpEad extends AbstractEntity
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
        'titulo' => 'string',
        'descricao' => '?string',
        'tipo_arquivo' => 'string',
        'arquivo_pdf' => '?string',
        'arquivo_video' => '?string',
    ];
}
