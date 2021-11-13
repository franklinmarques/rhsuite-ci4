<?php

namespace App\Entities;

class Deficiencia extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'tipo' => 'string',
    ];
}
