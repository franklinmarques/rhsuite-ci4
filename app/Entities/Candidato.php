<?php

namespace App\Entities;

class Candidato extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_empresa' => 'int',
        'nome' => 'string',
        'data_nascimento' => '?date',
        'sexo' => '?string',
        'estado_civil' => '?int',
        'nome_mae' => '?string',
        'nome_pai' => '?string',
        'cpf' => '?string',
        'rg' => '?string',
        'pis' => '?string',
        'logradouro' => '?string',
        'numero' => '?int',
        'complemento' => '?string',
        'bairro' => '?string',
        'id_cidade' => '?int',
        'id_estado' => '?int',
        'cep' => '?string',
        'id_escolaridade' => '?int',
        'id_deficiencia' => '?int',
        'foto' => '?string',
        'telefone' => 'string',
        'email' => 'string',
        'senha' => 'string',
        'token' => 'string',
        'data_inscricao' => '?datetime',
        'fonte_contratacao' => '?string',
        'data_edicao' => '?datetime',
        'nivel_acesso' => 'string',
        'url' => '?string',
        'arquivo_curriculo' => '?string',
        'status' => 'string',
    ];
}
