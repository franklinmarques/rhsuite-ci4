<?php

namespace App\Entities;

class FacilityVistoria extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_item' => 'int',
        'nome' => 'string',
    ];
}
