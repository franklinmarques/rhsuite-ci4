<?php

namespace App\Entities;

class RequisicaoPessoalTeste extends AbstractEntity
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
        'tipo_teste' => 'string',
        'id_modelo' => '?int',
        'nome' => '?string',
        'data_inicio' => 'datetime',
        'data_termino' => '?datetime',
        'minutos_duracao' => '?int',
        'aleatorizacao' => '?string',
        'data_acesso' => '?datetime',
        'data_envio' => '?datetime',
        'nota_aproveitamento' => '?decimal',
        'observacoes' => '?string',
        'status' => '?string',
    ];
}
