<?php

namespace App\Entities;

class IcomSessaoLibras extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_produto' => 'int',
        'id_cliente' => 'int',
        'codigo_contrato' => '?int',
        'data_evento' => 'date',
        'id_recursao' => '?int',
        'tipo_recursao' => '?bool',
        'data_inicio_recursao' => '?date',
        'data_termino_recursao' => '?date',
        'semanas_recursao' => '?string',
        'qtde_recursoes' => '?int',
        'horario_inicio' => 'time',
        'horario_termino' => 'time',
        'qtde_horas' => 'decimal',
        'titulo_evento' => '?string',
        'local_evento' => '?string',
        'requisitante_evento' => '?string',
        'valor_faturamento' => '?decimal',
        'valor_desconto' => '?decimal',
        'custo_operacional' => '?decimal',
        'custo_impostos' => '?decimal',
        'nota_fiscal_faturamento' => '?string',
        'id_depto_prestador_servico' => 'int',
        'id_profissional_alocado' => '?int',
        'valor_faturamento_profissional' => '?decimal',
        'valor_pagamento_profissional' => '?decimal',
        'nota_fiscal_pagamento' => '?string',
        'status' => 'bool',
        'data_criacao' => '?date',
        'data_cancelamento' => '?date',
        'horario_cancelamento' => '?time',
        'observacoes' => '?string',
    ];
}
