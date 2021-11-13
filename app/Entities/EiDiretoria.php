<?php

namespace App\Entities;

class EiDiretoria extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_diretoria' => 'int',
        'id_curso' => 'int',
    ];
}
