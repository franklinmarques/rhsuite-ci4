<?php

namespace App\Entities;

class RecrutamentoGoogle extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'cliente' => '?string',
        'cargo' => '?string',
        'cidade' => '?string',
        'nome' => 'string',
        'data_nascimento' => '?date',
        'deficiencia' => '?string',
        'telefone' => '?string',
        'email' => '?string',
        'fonte_contratacao' => '?string',
        'status' => '?string',
        'data_entrevista_rh' => '?string',
        'resultado_entrevista_rh' => '?string',
        'data_entrevista_cliente' => '?string',
        'resultado_entrevista_cliente' => '?string',
        'observacoes' => '?string',
    ];
}
