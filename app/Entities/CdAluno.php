<?php

namespace App\Entities;

class CdAluno extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'nome' => 'string',
        'id_escola' => 'int',
        'endereco' => '?string',
        'numero' => '?int',
        'complemento' => '?string',
        'municipio' => '?string',
        'telefone' => '?string',
        'contato' => '?string',
        'email' => '?string',
        'cep' => '?string',
        'hipotese_diagnostica' => 'string',
        'nome_responsavel' => '?string',
        'observacoes' => '?string',
        'data_matricula' => '?date',
        'data_afastamento' => '?date',
        'data_desligamento' => '?date',
        'periodo_manha' => 'int',
        'periodo_tarde' => 'int',
        'periodo_noite' => 'int',
        'status' => 'string',
    ];
}
