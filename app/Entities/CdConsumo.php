<?php

namespace App\Entities;

class CdConsumo extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_frequencia' => 'int',
        'id_insumo' => 'int',
        'qtde' => 'int',
    ];
}
