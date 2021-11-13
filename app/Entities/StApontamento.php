<?php

namespace App\Entities;

class StApontamento extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_alocado' => 'int',
        'data' => 'date',
        'hora_entrada' => '?datetime',
        'hora_intervalo' => '?datetime',
        'hora_retorno' => '?datetime',
        'hora_saida' => '?datetime',
        'qtde_dias' => '?int',
        'hora_atraso' => '?time',
        'qtde_req' => '?int',
        'qtde_rev' => '?int',
        'apontamento_extra' => '?time',
        'apontamento_desc' => '?time',
        'apontamento_saldo' => '?time',
        'apontamento_saldo_old' => '?time',
        'hora_glosa' => '?time',
        'id_detalhe_evento' => '?int',
        'observacoes' => '?string',
        'status' => 'string',
        'id_usuario_alocado_bck' => '?int',
    ];
}
