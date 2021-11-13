<?php

namespace App\Entities;

class EiFaturamentoConsolidado extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_medicao_mensal' => '?int',
        'id_alocacao' => 'int',
        'cargo' => 'string',
        'funcao' => 'string',
        'valor_hora_mes1' => 'decimal',
        'valor_hora_mes2' => 'decimal',
        'valor_hora_mes3' => 'decimal',
        'valor_hora_mes4' => 'decimal',
        'valor_hora_mes5' => 'decimal',
        'valor_hora_mes6' => 'decimal',
        'valor_hora_mes7' => 'decimal',
        'total_horas_mes1' => 'string',
        'total_horas_mes2' => 'string',
        'total_horas_mes3' => 'string',
        'total_horas_mes4' => 'string',
        'total_horas_mes5' => 'string',
        'total_horas_mes6' => 'string',
        'total_horas_mes7' => 'string',
        'valor_faturado_mes1' => '?decimal',
        'valor_faturado_mes2' => '?decimal',
        'valor_faturado_mes3' => '?decimal',
        'valor_faturado_mes4' => '?decimal',
        'valor_faturado_mes5' => '?decimal',
        'valor_faturado_mes6' => '?decimal',
        'valor_faturado_mes7' => '?decimal',
        'total_escolas' => '?int',
        'total_alunos' => '?int',
        'total_cuidadores' => '?int',
        'total_horas_projetadas' => '?string',
        'total_horas_realizadas' => '?string',
        'receita_projetada' => '?decimal',
        'receita_efetuada' => '?decimal',
        'pagamentos_efetuados' => '?decimal',
        'resultado' => '?decimal',
        'resultado_percentual' => '?decimal',
    ];
}
