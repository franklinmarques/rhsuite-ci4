<?php

namespace App\Entities;

class Tenant extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'url' => 'string',
        'email' => 'string',
    ];
}
