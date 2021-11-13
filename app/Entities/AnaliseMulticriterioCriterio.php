<?php

namespace App\Entities;

class AnaliseMulticriterioCriterio extends AbstractEntity
{
	protected $datamap = [];

	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	
	protected $casts   = [
        'id' => 'int',
        'id_dimensao' => 'int',
        'nome' => 'string',
        'descricao' => '?string',
        'peso' => '?int',
    ];
}
