<?php

namespace App\Entities;

class FacilityAndar extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_unidade' => 'int',
        'andar' => 'string',
    ];
}
