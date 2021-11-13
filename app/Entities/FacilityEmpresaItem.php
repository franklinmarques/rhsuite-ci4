<?php

namespace App\Entities;

class FacilityEmpresaItem extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_facility_empresa' => '?int',
        'nome' => 'string',
        'ativo' => 'bool',
    ];
}
