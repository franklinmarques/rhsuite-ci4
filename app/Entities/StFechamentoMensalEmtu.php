<?php

namespace App\Entities;

class StFechamentoMensalEmtu extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_alocacao' => 'int',
        'mes' => 'int',
        'ano' => 'int',
        'unidade' => 'string',
        'qtde_dados' => '?int',
        'qtde_digitadores' => '?int',
        'qtde_dias_uteis' => '?int',
        'valor_receita' => '?decimal',
        'valor_custo_fixo' => '?decimal',
        'valor_custo_variavel' => '?decimal',
        'valor_custo_total' => '?decimal',
        'valor_resultado' => '?decimal',
        'resultado_percentual' => '?decimal',
    ];
}
