<?php

namespace App\Entities;

class RecrutamentoCandidato extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_cargo' => 'int',
        'id_usuario' => 'int',
    ];
}
