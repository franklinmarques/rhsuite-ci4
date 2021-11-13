<?php

namespace App\Entities;

class FacilityContaDespesa extends AbstractEntity
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
        'valor' => 'decimal',
        'data_vencimento' => 'date',
        'mes' => 'int',
        'ano' => 'int',
    ];
}
