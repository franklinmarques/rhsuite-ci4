<?php

namespace App\Entities;

class CdSupervisor extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_supervisor' => 'int',
        'id_escola' => 'int',
        'turno' => 'string',
    ];
}
