<?php

namespace App\Entities;

class DimensionamentoEtapa extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_atividade' => 'int',
        'nome' => 'string',
        'tipo_atividade' => '?bool',
        'grau_complexidade' => '?bool',
        'tamanho_item' => '?bool',
        'peso_item' => '?decimal',
    ];
}
