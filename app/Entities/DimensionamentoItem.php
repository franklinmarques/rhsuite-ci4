<?php

namespace App\Entities;

class DimensionamentoItem extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_etapa' => 'int',
        'nome' => 'string',
        'descricao' => '?string',
        'unidade_medida' => '?string',
        'valor' => '?decimal',
    ];
}
