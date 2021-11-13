<?php

namespace App\Entities;

class RecrutamentoTeste extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_candidato' => 'int',
        'id_modelo' => 'int',
        'data_inicio' => 'datetime',
        'data_termino' => 'datetime',
        'minutos_duracao' => '?int',
        'aleatorizacao' => '?string',
        'data_acesso' => '?datetime',
        'data_envio' => '?datetime',
        'status' => '?string',
    ];
}
