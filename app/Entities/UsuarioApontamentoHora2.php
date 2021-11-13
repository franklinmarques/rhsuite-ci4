<?php

namespace App\Entities;

class UsuarioApontamentoHora2 extends AbstractEntity
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
        'id_old' => '?int',
        'data_hora' => 'datetime',
        'turno_evento' => 'string',
        'numero_turno' => '?int',
        'data_hora_entrada' => 'datetime',
        'tipo_evento_entrada' => 'string',
        'data_hora_saida' => '?datetime',
        'tipo_evento_saida' => '?string',
        'latitude' => '?decimal',
        'longitude' => '?decimal',
        'saldo_horas' => '?time',
        'saldo_horas_2' => '?time',
        'banco_horas' => '?time',
        'descontos_folha' => '?time',
        'modo_automatico' => '?bool',
        'entrada_automatica' => '?bool',
        'saida_automatica' => '?bool',
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
