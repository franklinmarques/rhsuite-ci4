<?php

namespace App\Entities;

class CompraOrcamento extends AbstractEntity
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
        'id_requisicao' => '?int',
        'data' => 'date',
        'tipo' => 'bool',
        'id_subtipo' => '?int',
    ];
}
