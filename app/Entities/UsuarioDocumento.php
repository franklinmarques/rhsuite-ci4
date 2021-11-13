<?php

namespace App\Entities;

class UsuarioDocumento extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_usuario' => 'int',
        'tipo' => 'bool',
        'nome' => 'string',
        'arquivo' => '?string',
        'data_inicio' => '?date',
        'data_termino' => '?date',
        'valor_hora_periodo' => '?decimal',
        'valor_mensal' => '?decimal',
        'qtde_horas_mensais' => '?time',
        'localidade' => '?string',
        'status_ativo' => '?bool',
    ];
}
