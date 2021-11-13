<?php

namespace App\Entities;

class FacilityContaUnidade extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_conta_empresa' => 'int',
        'nome' => 'string',
    ];
}
