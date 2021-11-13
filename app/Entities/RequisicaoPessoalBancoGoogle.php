<?php

namespace App\Entities;

class RequisicaoPessoalBancoGoogle extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_requisicao' => 'int',
        'cliente' => 'int',
        'nome_candidato' => 'string',
        'cargo' => 'int',
        'cidade' => 'int',
        'deficiencia' => '?int',
        'telefone' => '?string',
        'fonte_contratacao' => '?int',
        'data_captacao' => '?date',
        'data_entrevista_rh' => '?date',
        'resultado-entrevista_rh' => '?int',
        'data_entrevista_cliente' => '?date',
        'resultado_entrevista_cliente' => '?int',
        'status' => '?int',
        'observacoes' => '?string',
    ];
}
