<?php

namespace App\Entities;

class RecrutamentoUsuario extends AbstractEntity
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
        'rg_orgao_emissor' => '?string',
        'rg_data_emissao' => '?date',
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
        'email' => '?string',
        'senha' => '?string',
        'token' => 'string',
        'data_inscricao' => '?datetime',
        'fonte_contratacao' => '?string',
        'resumo_cv' => '?string',
        'profissao_cargo_funcao_1' => '?string',
        'profissao_cargo_funcao_2' => '?string',
        'data_edicao' => '?datetime',
        'nivel_acesso' => 'string',
        'observacoes' => '?string',
        'arquivo_curriculo' => '?string',
        'arquivo_laudo_medico' => '?string',
        'status' => 'string',
        'status_aceite' => '?bool',
        'data_hora_aceite' => '?datetime',
        'spa' => '?bool',
    ];
}
