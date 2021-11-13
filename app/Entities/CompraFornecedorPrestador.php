<?php

namespace App\Entities;

class CompraFornecedorPrestador extends AbstractEntity
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
        'nome' => 'string',
        'tipo' => 'bool',
        'id_subtipo' => 'int',
        'vinculo' => '?bool',
        'vinculo_old' => '?string',
        'pessoa_contato' => '?string',
        'telefone' => '?string',
        'email' => '?string',
        'status' => 'bool',
    ];
}
