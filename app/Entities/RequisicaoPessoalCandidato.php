<?php

namespace App\Entities;

class RequisicaoPessoalCandidato extends AbstractEntity
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
        'id_usuario' => '?int',
        'id_usuario_banco' => '?int',
        'data_inscricao' => '?datetime',
        'status' => '?string',
        'data_selecao' => '?datetime',
        'resultado_selecao' => '?string',
        'data_requisitante' => '?datetime',
        'resultado_requisitante' => '?string',
        'antecedentes_criminais' => '?bool',
        'restricoes_financeiras' => '?bool',
        'data_exame_admissional' => '?datetime',
        'endereco_exame_admissional' => '?string',
        'resultado_exame_admissional' => '?bool',
        'aprovado' => '?bool',
        'aprovado_indicacao' => '?bool',
        'data_admissao' => '?date',
        'observacoes' => '?string',
        'desempenho_perfil' => '?string',
    ];
}
