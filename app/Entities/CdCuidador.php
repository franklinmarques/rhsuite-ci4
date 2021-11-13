<?php

namespace App\Entities;

class CdCuidador extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_cuidador' => 'int',
        'id_escola' => 'int',
        'id_supervisor' => '?int',
        'turno' => 'string',
    ];
}
