<?php

namespace App\Entities;

class Atividade extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_atividade_mae' => '?int',
        'id_usuario' => 'int',
        'tipo' => 'string',
        'prioridade' => 'int',
        'atividade' => 'string',
        'data_cadastro' => 'datetime',
        'data_limite' => 'datetime',
        'data_lembrete' => 'date',
        'data_fechamento' => '?datetime',
        'status' => 'int',
        'observacoes' => '?string',
    ];
}
