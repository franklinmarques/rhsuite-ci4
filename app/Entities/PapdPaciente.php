<?php

namespace App\Entities;

class PapdPaciente extends AbstractEntity
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
        'cpf' => '?string',
        'data_nascimento' => 'date',
        'sexo' => 'string',
        'id_deficiencia' => '?int',
        'cadastro_municipal' => '?string',
        'id_hipotese_diagnostica' => '?int',
        'logradouro' => '?string',
        'numero' => '?int',
        'complemento' => '?string',
        'bairro' => '?string',
        'cidade' => '?int',
        'cidade_nome' => '?string',
        'estado' => '?int',
        'cep' => '?string',
        'nome_responsavel_1' => '?string',
        'telefone_fixo_1' => '?string',
        'nome_responsavel_2' => '?string',
        'telefone_fixo_2' => '?string',
        'telefone_celular_2' => '?string',
        'data_ingresso' => 'date',
        'data_inativo' => '?date',
        'data_fila_espera' => '?date',
        'data_afastamento' => '?date',
        'contratante' => '?string',
        'contrato' => '?string',
        'id_instituicao' => 'int',
        'status' => 'string',
        'telefone_celular_1' => '?string',
    ];
}
