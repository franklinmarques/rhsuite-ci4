<?php

namespace App\Entities;

class StAlocacaoObservacao extends AbstractEntity
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
        'total_colaboradores_contratados' => '?int',
        'total_colaboradores_ativos' => '?int',
        'visitas_projetadas' => '?int',
        'visitas_realizadas' => '?int',
        'visitas_porcentagem' => '?int',
        'visitas_total_horas' => '?int',
        'balanco_valor_projetado' => '?decimal',
        'balanco_glosas' => '?decimal',
        'balanco_valor_glosa' => '?decimal',
        'balanco_porcentagem' => '?decimal',
        'turnover_admissoes' => '?int',
        'turnover_demissoes' => '?int',
        'turnover_desligamentos' => '?int',
        'atendimentos_total_mes' => '?int',
        'atendimentos_media_diaria' => '?int',
        'pendencias_total_informada' => '?int',
        'pendencias_aguardando_tratativa' => '?int',
        'pendencias_parcialmente_resolvidas' => '?int',
        'pendencias_resolvidas' => '?int',
        'pendencias_resolvidas_atendimentos' => '?int',
        'monitoria_media_equipe' => '?int',
        'indicadores_operacionais_tma' => '?time',
        'indicadores_operacionais_tme' => '?time',
        'indicadores_operacionais_ociosidade' => '?time',
        'avaliacoes_atendimento' => '?int',
        'avaliacoes_atendimento_otimos' => '?int',
        'avaliacoes_atendimento_bons' => '?int',
        'avaliacoes_atendimento_regulares' => '?int',
        'avaliacoes_atendimento_ruins' => '?int',
        'solicitacoes' => '?int',
        'solicitacoes_atendidas' => '?int',
        'solicitacoes_nao_atendidas' => '?int',
        'observacoes' => '?string',
    ];
}
