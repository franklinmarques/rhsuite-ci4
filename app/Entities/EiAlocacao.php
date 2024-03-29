<?php

namespace App\Entities;

class EiAlocacao extends AbstractEntity
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
        'depto' => 'string',
        'id_diretoria' => 'int',
        'diretoria' => 'string',
        'id_supervisor' => 'int',
        'supervisor' => 'string',
        'municipio' => 'string',
        'coordenador' => 'string',
        'ano' => 'int',
        'semestre' => 'bool',
        'id_ordem_servico' => '?int',
        'ordem_servico' => '?string',
        'congelar_mes1' => '?bool',
        'congelar_mes2' => '?bool',
        'congelar_mes3' => '?bool',
        'congelar_mes4' => '?bool',
        'congelar_mes5' => '?bool',
        'congelar_mes6' => '?bool',
        'congelar_mes7' => '?bool',
        'pagamento_fracionado_mes1' => '?bool',
        'pagamento_fracionado_mes2' => '?bool',
        'pagamento_fracionado_mes3' => '?bool',
        'pagamento_fracionado_mes4' => '?bool',
        'pagamento_fracionado_mes5' => '?bool',
        'pagamento_fracionado_mes6' => '?bool',
        'pagamento_fracionado_mes7' => '?bool',
        'medicao_liberada_mes1' => '?bool',
        'medicao_liberada_mes2' => '?bool',
        'medicao_liberada_mes3' => '?bool',
        'medicao_liberada_mes4' => '?bool',
        'medicao_liberada_mes5' => '?bool',
        'medicao_liberada_mes6' => '?bool',
        'medicao_liberada_mes7' => '?bool',
        'dia_fechamento_mes1' => '?int',
        'dia_fechamento_mes2' => '?int',
        'dia_fechamento_mes3' => '?int',
        'dia_fechamento_mes4' => '?int',
        'dia_fechamento_mes5' => '?int',
        'dia_fechamento_mes6' => '?int',
        'dia_fechamento_mes7' => '?int',
        'saldo_mes1' => '?string',
        'saldo_mes2' => '?string',
        'saldo_mes3' => '?string',
        'saldo_mes4' => '?string',
        'saldo_mes5' => '?string',
        'saldo_mes6' => '?string',
        'saldo_mes7' => '?string',
        'saldo_acumulado_mes1' => '?string',
        'saldo_acumulado_mes2' => '?string',
        'saldo_acumulado_mes3' => '?string',
        'saldo_acumulado_mes4' => '?string',
        'saldo_acumulado_mes5' => '?string',
        'saldo_acumulado_mes6' => '?string',
        'saldo_acumulado_mes7' => '?string',
        'observacoes_mes1' => '?string',
        'observacoes_mes2' => '?string',
        'observacoes_mes3' => '?string',
        'observacoes_mes4' => '?string',
        'observacoes_mes5' => '?string',
        'observacoes_mes6' => '?string',
        'observacoes_mes7' => '?string',
        'total_horas_mes1' => '?string',
        'total_horas_mes2' => '?string',
        'total_horas_mes3' => '?string',
        'total_horas_mes4' => '?string',
        'total_horas_mes5' => '?string',
        'total_horas_mes6' => '?string',
        'total_horas_mes7' => '?string',
        'valor_faturado_mes1' => '?decimal',
        'valor_faturado_mes2' => '?decimal',
        'valor_faturado_mes3' => '?decimal',
        'valor_faturado_mes4' => '?decimal',
        'valor_faturado_mes5' => '?decimal',
        'valor_faturado_mes6' => '?decimal',
        'valor_faturado_mes7' => '?decimal',
    ];
}
