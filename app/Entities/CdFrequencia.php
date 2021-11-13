<?php

namespace App\Entities;

class CdFrequencia extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_matriculado' => 'int',
        'data' => 'date',
        'status' => '?string',
    ];
}
