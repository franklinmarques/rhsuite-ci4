<?php

namespace App\Entities;

class FacilityModelo extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_empresa' => 'int',
        'id_facility_empresa' => 'int',
        'nome' => 'string',
        'tipo' => '?string',
        'versao' => 'string',
        'status' => 'bool',
        'id_copia' => '?int',
    ];
}
