<?php

namespace App\Entities;

class AvaliacaoExpPeriodo extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id_avaliado' => 'int',
        'pontos_fortes' => '?string',
        'pontos_fracos' => '?string',
        'feedback1' => '?string',
        'data_feedback1' => '?date',
        'feedback2' => '?string',
        'data_feedback2' => '?date',
        'feedback3' => '?string',
        'data_feedback3' => '?date',
        'parecer_final' => '?string',
        'data' => '?datetime',
    ];
}
