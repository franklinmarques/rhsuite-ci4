<?php

namespace App\Entities;

class AtividadeSchedulerMes extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id_atividade_scheduler' => 'int',
        'janeiro' => '?bool',
        'fevereiro' => '?bool',
        'marco' => '?bool',
        'abril' => '?bool',
        'maio' => '?bool',
        'junho' => '?bool',
        'julho' => '?bool',
        'agosto' => '?bool',
        'setembro' => '?bool',
        'outubro' => '?bool',
        'novembro' => '?bool',
        'dezembro' => '?bool',
    ];
}
