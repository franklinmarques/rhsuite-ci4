<?php

namespace App\Entities;

class AvaliacaoExpDesempenho extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id_avaliador' => 'int',
        'pontos_fortes' => '?string',
        'pontos_fracos' => '?string',
        'observacoes' => '?string',
        'data' => '?datetime',
    ];
}
