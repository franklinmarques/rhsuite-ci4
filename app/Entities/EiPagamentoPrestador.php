<?php

namespace App\Entities;

class EiPagamentoPrestador extends AbstractEntity
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
        'id_cuidador' => '?int',
        'cuidador' => '?string',
        'cargo' => '?string',
        'cargo_mes2' => '?string',
        'cargo_mes3' => '?string',
        'cargo_mes4' => '?string',
        'cargo_mes5' => '?string',
        'cargo_mes6' => '?string',
        'cargo_mes7' => '?string',
        'funcao' => '?string',
        'funcao_mes2' => '?string',
        'funcao_mes3' => '?string',
        'funcao_mes4' => '?string',
        'funcao_mes5' => '?string',
        'funcao_mes6' => '?string',
        'funcao_mes7' => '?string',
        'id_substituto' => '?int',
        'nome_substituto' => '?string',
        'funcao_substituto' => '?string',
        'mes_substituticao' => '?bool',
        'nota_complementar' => '?bool',
        'id_complementar_1' => '?int',
        'id_complementar_2' => '?int',
        'nota_fiscal_mes1' => '?string',
        'nota_fiscal_mes2' => '?string',
        'nota_fiscal_mes3' => '?string',
        'nota_fiscal_mes4' => '?string',
        'nota_fiscal_mes5' => '?string',
        'nota_fiscal_mes6' => '?string',
        'nota_fiscal_mes7' => '?string',
        'nota_fiscal_sub' => '?string',
        'data_solicitacao_nota_mes1' => '?date',
        'data_solicitacao_nota_mes2' => '?date',
        'data_solicitacao_nota_mes3' => '?date',
        'data_solicitacao_nota_mes4' => '?date',
        'data_solicitacao_nota_mes5' => '?date',
        'data_solicitacao_nota_mes6' => '?date',
        'data_solicitacao_nota_mes7' => '?date',
        'data_solicitacao_nota_sub' => '?date',
        'data_emissao_mes1' => '?date',
        'data_emissao_mes2' => '?date',
        'data_emissao_mes3' => '?date',
        'data_emissao_mes4' => '?date',
        'data_emissao_mes5' => '?date',
        'data_emissao_mes6' => '?date',
        'data_emissao_mes7' => '?date',
        'data_emissao_sub' => '?date',
        'codigo_alfa_mes1' => '?string',
        'codigo_alfa_mes2' => '?string',
        'codigo_alfa_mes3' => '?string',
        'codigo_alfa_mes4' => '?string',
        'codigo_alfa_mes5' => '?string',
        'codigo_alfa_mes6' => '?string',
        'codigo_alfa_mes7' => '?string',
        'codigo_alfa_sub' => '?string',
        'valor_extra1_mes1' => '?decimal',
        'valor_extra1_mes2' => '?decimal',
        'valor_extra1_mes3' => '?decimal',
        'valor_extra1_mes4' => '?decimal',
        'valor_extra1_mes5' => '?decimal',
        'valor_extra1_mes6' => '?decimal',
        'valor_extra1_mes7' => '?decimal',
        'valor_extra1_sub' => '?decimal',
        'valor_extra2_mes1' => '?decimal',
        'valor_extra2_mes2' => '?decimal',
        'valor_extra2_mes3' => '?decimal',
        'valor_extra2_mes4' => '?decimal',
        'valor_extra2_mes5' => '?decimal',
        'valor_extra2_mes6' => '?decimal',
        'valor_extra2_mes7' => '?decimal',
        'valor_extra2_sub' => '?decimal',
        'justificativa1_mes1' => '?string',
        'justificativa1_mes2' => '?string',
        'justificativa1_mes3' => '?string',
        'justificativa1_mes4' => '?string',
        'justificativa1_mes5' => '?string',
        'justificativa1_mes6' => '?string',
        'justificativa1_sub' => '?string',
        'justificativa1_mes7' => '?string',
        'justificativa2_mes1' => '?string',
        'justificativa2_mes2' => '?string',
        'justificativa2_mes3' => '?string',
        'justificativa2_mes4' => '?string',
        'justificativa2_mes5' => '?string',
        'justificativa2_mes6' => '?string',
        'justificativa2_mes7' => '?string',
        'justificativa2_sub' => '?string',
        'data_liberacao_pagto_mes1' => '?date',
        'data_liberacao_pagto_mes2' => '?date',
        'data_liberacao_pagto_mes3' => '?date',
        'data_liberacao_pagto_mes4' => '?date',
        'data_liberacao_pagto_mes5' => '?date',
        'data_liberacao_pagto_mes6' => '?date',
        'data_liberacao_pagto_mes7' => '?date',
        'data_liberacao_pagto_sub' => '?date',
        'data_inicio_contrato_mes1' => '?date',
        'data_inicio_contrato_mes2' => '?date',
        'data_inicio_contrato_mes3' => '?date',
        'data_inicio_contrato_mes4' => '?date',
        'data_inicio_contrato_mes5' => '?date',
        'data_inicio_contrato_mes6' => '?date',
        'data_inicio_contrato_mes7' => '?date',
        'data_inicio_contrato_sub' => '?date',
        'data_termino_contrato_mes1' => '?date',
        'data_termino_contrato_mes2' => '?date',
        'data_termino_contrato_mes3' => '?date',
        'data_termino_contrato_mes4' => '?date',
        'data_termino_contrato_mes5' => '?date',
        'data_termino_contrato_mes6' => '?date',
        'data_termino_contrato_mes7' => '?date',
        'data_termino_contrato_sub' => '?date',
        'falta_anterior_mes1' => '?time',
        'falta_anterior_mes2' => '?time',
        'falta_anterior_mes3' => '?time',
        'falta_anterior_mes4' => '?time',
        'falta_anterior_mes5' => '?time',
        'falta_anterior_mes6' => '?time',
        'falta_anterior_mes7' => '?time',
        'falta_anterior_sub' => '?time',
        'falta_atual_mes1' => '?time',
        'falta_atual_mes2' => '?time',
        'falta_atual_mes3' => '?time',
        'falta_atual_mes4' => '?time',
        'falta_atual_mes5' => '?time',
        'falta_atual_mes6' => '?time',
        'falta_atual_mes7' => '?time',
        'falta_atual_sub' => '?time',
        'falta_posterior_mes7' => '?time',
        'desconto_anterior_mes1' => '?time',
        'desconto_anterior_mes2' => '?time',
        'desconto_anterior_mes3' => '?time',
        'desconto_anterior_mes4' => '?time',
        'desconto_anterior_mes5' => '?time',
        'desconto_anterior_mes6' => '?time',
        'desconto_anterior_mes7' => '?time',
        'desconto_anterior_sub' => '?time',
        'desconto_atual_mes1' => '?time',
        'desconto_atual_mes2' => '?time',
        'desconto_atual_mes3' => '?time',
        'desconto_atual_mes4' => '?time',
        'desconto_atual_mes5' => '?time',
        'desconto_atual_mes6' => '?time',
        'desconto_atual_mes7' => '?time',
        'desconto_atual_sub' => '?time',
        'desconto_posterior_mes7' => '?time',
        'pagamento_proporcional_inicio' => '?bool',
        'pagamento_proporcional_termino' => '?bool',
        'tipo_pagamento_mes1' => '?bool',
        'tipo_pagamento_mes2' => '?bool',
        'tipo_pagamento_mes3' => '?bool',
        'tipo_pagamento_mes4' => '?bool',
        'tipo_pagamento_mes5' => '?bool',
        'tipo_pagamento_mes6' => '?bool',
        'tipo_pagamento_mes7' => '?bool',
        'tipo_pagamento_sub' => '?bool',
        'status_mes1' => '?bool',
        'status_mes2' => '?bool',
        'status_mes3' => '?bool',
        'status_mes4' => '?bool',
        'status_mes5' => '?bool',
        'status_mes6' => '?bool',
        'status_mes7' => '?bool',
        'status_sub' => '?bool',
        'arquivo_nota_fiscal_mes1' => '?string',
        'arquivo_nota_fiscal_mes2' => '?string',
        'arquivo_nota_fiscal_mes3' => '?string',
        'arquivo_nota_fiscal_mes4' => '?string',
        'arquivo_nota_fiscal_mes5' => '?string',
        'arquivo_nota_fiscal_mes6' => '?string',
        'arquivo_nota_fiscal_mes7' => '?string',
        'arquivo_nota_fiscal_sub' => '?string',
        'validacao_nota_fiscal_mes1' => '?bool',
        'validacao_nota_fiscal_mes2' => '?bool',
        'validacao_nota_fiscal_mes3' => '?bool',
        'validacao_nota_fiscal_mes4' => '?bool',
        'validacao_nota_fiscal_mes5' => '?bool',
        'validacao_nota_fiscal_mes6' => '?bool',
        'validacao_nota_fiscal_mes7' => '?bool',
        'validacao_nota_fiscal_sub' => '?bool',
        'observacoes_mes1' => '?string',
        'observacoes_mes2' => '?string',
        'observacoes_mes3' => '?string',
        'observacoes_mes4' => '?string',
        'observacoes_mes5' => '?string',
        'observacoes_mes6' => '?string',
        'observacoes_mes7' => '?string',
        'observacoes_sub' => '?string',
        'mes_competencia_1' => '?bool',
        'mes_competencia_2' => '?bool',
        'mes_competencia_3' => '?bool',
        'mes_competencia_4' => '?bool',
        'mes_competencia_5' => '?bool',
        'mes_competencia_6' => '?bool',
        'mes_competencia_7' => '?bool',
        'mes_competencia_sub' => '?bool',
        'preservar_edicao_mes1' => '?bool',
        'preservar_edicao_mes2' => '?bool',
        'preservar_edicao_mes3' => '?bool',
        'preservar_edicao_mes4' => '?bool',
        'preservar_edicao_mes5' => '?bool',
        'preservar_edicao_mes6' => '?bool',
        'preservar_edicao_mes7' => '?bool',
        'preservar_edicao_sub' => '?bool',
        'data_criacao_mes1' => '?datetime',
        'data_criacao_mes2' => '?datetime',
        'data_criacao_mes3' => '?datetime',
        'data_criacao_mes4' => '?datetime',
        'data_criacao_mes5' => '?datetime',
        'data_criacao_mes6' => '?datetime',
        'data_criacao_mes7' => '?datetime',
        'data_criacao_sub' => '?datetime',
    ];
}