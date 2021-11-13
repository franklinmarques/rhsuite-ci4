<?php

namespace App\Entities;

class AvaliacaoExpAvaliado extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_modelo' => 'int',
        'id_avaliado' => 'int',
        'id_supervisor' => '?int',
        'data_atividades' => 'datetime',
        'nota_corte' => 'int',
        'observacoes' => '?string',
        'id_avaliacao' => '?int',
    ];
}
