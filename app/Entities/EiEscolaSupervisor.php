<?php

namespace App\Entities;

class EiEscolaSupervisor extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_escola' => 'int',
        'id_supervisor' => 'int',
        'id_usuario' => 'int',
        'turno' => '?string',
    ];
}
