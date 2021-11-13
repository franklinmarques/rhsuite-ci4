<?php

namespace App\Entities;

class AnaliseSwotAmbienteExterno extends AbstractEntity
{
	protected $datamap = [];

	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	
	protected $casts   = [
        'id' => 'int',
        'id_analise' => 'int',
        'status' => 'bool',
        'risco_oportunidade' => 'string',
    ];
}
