<?php

namespace App\Entities;

class GestaoVaga extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'codigo' => 'int',
        'id_empresa' => 'int',
        'data_abertura' => 'date',
        'status' => 'bool',
        'id_requisicao_pessoal' => 'int',
        'id_cargo' => '?int',
        'id_funcao' => '?int',
        'cargo_funcao_alternativo' => '?string',
        'formacao_minima' => '?int',
        'formacao_especifica_minima' => '?string',
        'perfil_profissional_desejado' => '?string',
        'quantidade' => 'int',
        'estado_vaga' => '?string',
        'cidade_vaga' => '?string',
        'bairro_vaga' => '?string',
        'tipo_vinculo' => 'bool',
        'remuneracao' => 'decimal',
        'beneficios' => '?string',
        'horario_trabalho' => '?string',
        'contato_selecionador' => '?string',
    ];
}
