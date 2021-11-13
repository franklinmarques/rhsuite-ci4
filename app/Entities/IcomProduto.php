<?php

namespace App\Entities;

class IcomProduto extends AbstractEntity
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
        'id_setor' => 'int',
        'codigo' => 'string',
        'nome' => 'string',
        'tipo' => 'string',
        'dupla' => '?bool',
        'preco' => 'decimal',
        'custo' => '?decimal',
        'tipo_cobranca' => 'string',
        'centro_custo' => '?string',
        'complementos' => '?string',
    ];
}
