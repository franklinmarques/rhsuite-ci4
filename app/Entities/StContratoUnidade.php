<?php

namespace App\Entities;

class StContratoUnidade extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_contrato' => 'int',
        'setor' => 'string',
    ];
}
