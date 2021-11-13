<?php

namespace App\Entities;

class AnaliseAdlMaturidade extends AbstractEntity
{
	protected $datamap = [];

	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	
	protected $casts   = [
        'id' => 'int',
        'id_produto' => 'int',
        'grau_maturidade' => 'int',
    ];
}
