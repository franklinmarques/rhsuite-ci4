<?php

namespace App\Entities;

class StContratoReajuste extends AbstractEntity
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
        'data_reajuste' => 'date',
        'valor_indice' => 'decimal',
    ];
}
