<?php

namespace App\Entities;

class EiLogPagamentoPrestador extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'data' => 'datetime',
        'mes_faturamento' => '?int',
        'id_usuario' => 'int',
        'nome_usuario' => 'string',
        'escola' => 'string',
        'colaborador' => 'string',
        'alunos' => '?string',
        'observacoes' => '?string',
        'quantidades' => '?string',
        'valores' => '?string',
        'valores_totais' => '?string',
    ];
}
