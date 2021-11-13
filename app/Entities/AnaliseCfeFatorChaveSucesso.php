<?php

namespace App\Entities;

class AnaliseCfeFatorChaveSucesso extends AbstractEntity
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
        'fator_chave' => 'string',
        'peso' => '?int',
        'impacto' => '?int',
        'resultado' => '?int',
    ];
}
