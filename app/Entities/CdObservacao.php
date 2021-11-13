<?php

namespace App\Entities;

class CdObservacao extends AbstractEntity
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
        'id_supervisor' => '?int',
        'supervisor' => 'string',
        'total_faltas' => '?int',
        'total_faltas_justificadas' => '?int',
        'turnover_substituicao' => '?int',
        'turnover_aumento_quadro' => '?int',
        'turnover_desligamento_empresa' => '?int',
        'turnover_desligamento_solicitacao' => '?int',
        'intercorrencias_diretoria' => '?int',
        'intercorrencias_cuidador' => '?int',
        'intercorrencias_alunos' => '?int',
        'acidentes_trabalho' => '?int',
        'total_escolas' => '?int',
        'total_alunos' => '?int',
        'dias_letivos' => '?int',
        'total_cuidadores' => '?int',
        'total_cuidadores_cobrados' => '?int',
        'total_cuidadores_ativos' => '?int',
        'total_cuidadores_afastados' => '?int',
        'total_supervisores' => '?int',
        'total_supervisores_cobrados' => '?int',
        'total_supervisores_ativos' => '?int',
        'total_supervisores_afastados' => '?int',
        'faturamento_projetado' => '?decimal',
        'faturamento_realizado' => '?decimal',
    ];
}
