<?php

namespace App\Entities;

class AnaliseGeForcaNegocio extends AbstractEntity
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
        'nome' => 'string',
        'peso' => '?int',
        'classificacao' => '?int',
        'indice_relativo' => '?float',
        'indice_padrao' => '?int',
    ];
}
