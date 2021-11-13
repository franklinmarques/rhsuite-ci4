<?php

namespace App\Entities;

class ArquivoTemp extends AbstractEntity
{
	protected $datamap = [];

	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	
	protected $casts   = [
        'id' => 'int',
        'id_usuario' => '?int',
        'arquivo' => '?string',
    ];
}
