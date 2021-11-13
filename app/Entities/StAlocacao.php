<?php

namespace App\Entities;

class StAlocacao extends AbstractEntity
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
        'data' => 'date',
        'depto' => 'string',
        'area' => 'string',
        'setor' => 'string',
        'ano' => 'int',
        'mes' => 'bool',
        'dia_fechamento' => '?int',
        'contrato' => '?string',
        'descricao_servico' => '?string',
        'valor_servico' => '?decimal',
        'qtde_alocados_potenciais' => '?int',
        'qtde_alocados_ativos' => '?int',
        'turnover_reposicao' => '?int',
        'turnover_aumento_quadro' => '?int',
        'turnover_desligamento_empresa' => '?int',
        'turnover_desligamento_colaborador' => '?int',
        'observacoes' => '?string',
        'valor_projetado' => '?decimal',
        'valor_realizado' => '?decimal',
        'total_faltas' => 'decimal',
        'total_dias_cobertos' => 'decimal',
        'total_dias_descobertos' => 'decimal',
        'mes_bloqueado' => '?bool',
    ];
}
