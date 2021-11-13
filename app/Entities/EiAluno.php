<?php

namespace App\Entities;

class EiAluno extends AbstractEntity
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
        'id_escola' => '?int',
        'endereco' => '?string',
        'numero' => '?int',
        'complemento' => '?string',
        'municipio' => '?string',
        'telefone' => '?string',
        'contato' => '?string',
        'email' => '?string',
        'cep' => '?string',
        'hipotese_diagnostica' => '?string',
        'nome_responsavel' => '?string',
        'observacoes' => '?string',
        'data_matricula' => '?date',
        'data_afastamento' => '?date',
        'data_desligamento' => '?date',
        'status' => 'string',
    ];
}
