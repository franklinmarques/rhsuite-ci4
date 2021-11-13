<?php

namespace App\Entities;

class DimensionamentoAtividade extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_processo' => 'int',
        'nome' => 'string',
    ];
}
