<?php

namespace App\Entities;

class AtividadeScheduler extends AbstractEntity
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
        'id_usuario' => '?int',
        'atividade' => 'string',
        'dia' => '?bool',
        'semana' => '?bool',
        'mes' => '?bool',
        'objetivos' => 'string',
        'data_cadastro' => 'date',
        'data_limite' => '?string',
        'envolvidos' => 'string',
        'observacoes' => '?string',
        'processo_roteiro' => '?string',
        'documento_1' => '?string',
        'documento_2' => '?string',
        'documento_3' => '?string',
        'lembrar' => 'bool',
    ];
}
