<?php

namespace App\Entities;

class UsuarioApontamentoHora extends AbstractEntity
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
        'data_hora' => 'datetime',
        'turno_evento' => 'string',
        'latitude' => '?decimal',
        'longitude' => '?decimal',
        'saldo_horas' => '?time',
        'banco_horas' => '?time',
        'descontos_folha' => '?time',
        'modo_cadastramento' => '?string',
        'id_depto' => '?int',
        'id_area' => '?int',
        'id_setor' => '?int',
        'justificativa' => '?string',
        'aceite_justificativa' => '?string',
        'data_aceite' => '?datetime',
        'observacoes_aceite' => '?string',
        'id_usuario_aceite' => '?int',
    ];
}
