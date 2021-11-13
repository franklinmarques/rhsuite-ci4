<?php

namespace App\Models;

use App\Entities\StAlocacaoObservacao;

class StAlocacaoObservacaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'st_alocacoes_observacoes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = StAlocacaoObservacao::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_alocacao',
        'total_colaboradores_contratados',
        'total_colaboradores_ativos',
        'visitas_projetadas',
        'visitas_realizadas',
        'visitas_porcentagem',
        'visitas_total_horas',
        'balanco_valor_projetado',
        'balanco_glosas',
        'balanco_valor_glosa',
        'balanco_porcentagem',
        'turnover_admissoes',
        'turnover_demissoes',
        'turnover_desligamentos',
        'atendimentos_total_mes',
        'atendimentos_media_diaria',
        'pendencias_total_informada',
        'pendencias_aguardando_tratativa',
        'pendencias_parcialmente_resolvidas',
        'pendencias_resolvidas',
        'pendencias_resolvidas_atendimentos',
        'monitoria_media_equipe',
        'indicadores_operacionais_tma',
        'indicadores_operacionais_tme',
        'indicadores_operacionais_ociosidade',
        'avaliacoes_atendimento',
        'avaliacoes_atendimento_otimos',
        'avaliacoes_atendimento_bons',
        'avaliacoes_atendimento_regulares',
        'avaliacoes_atendimento_ruins',
        'solicitacoes',
        'solicitacoes_atendidas',
        'solicitacoes_nao_atendidas',
        'observacoes',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_alocacao'                           => 'required|is_natural_no_zero|max_length[11]',
        'total_colaboradores_contratados'       => 'integer|max_length[11]',
        'total_colaboradores_ativos'            => 'integer|max_length[11]',
        'visitas_projetadas'                    => 'integer|max_length[11]',
        'visitas_realizadas'                    => 'integer|max_length[11]',
        'visitas_porcentagem'                   => 'integer|max_length[11]',
        'visitas_total_horas'                   => 'integer|max_length[11]',
        'balanco_valor_projetado'               => 'numeric|max_length[10]',
        'balanco_glosas'                        => 'numeric|max_length[10]',
        'balanco_valor_glosa'                   => 'numeric|max_length[10]',
        'balanco_porcentagem'                   => 'numeric|max_length[3]',
        'turnover_admissoes'                    => 'integer|max_length[11]',
        'turnover_demissoes'                    => 'integer|max_length[11]',
        'turnover_desligamentos'                => 'integer|max_length[11]',
        'atendimentos_total_mes'                => 'integer|max_length[11]',
        'atendimentos_media_diaria'             => 'integer|max_length[11]',
        'pendencias_total_informada'            => 'integer|max_length[11]',
        'pendencias_aguardando_tratativa'       => 'integer|max_length[11]',
        'pendencias_parcialmente_resolvidas'    => 'integer|max_length[11]',
        'pendencias_resolvidas'                 => 'integer|max_length[11]',
        'pendencias_resolvidas_atendimentos'    => 'integer|max_length[11]',
        'monitoria_media_equipe'                => 'integer|max_length[11]',
        'indicadores_operacionais_tma'          => 'valid_time',
        'indicadores_operacionais_tme'          => 'valid_time',
        'indicadores_operacionais_ociosidade'   => 'valid_time',
        'avaliacoes_atendimento'                => 'integer|max_length[11]',
        'avaliacoes_atendimento_otimos'         => 'integer|max_length[11]',
        'avaliacoes_atendimento_bons'           => 'integer|max_length[11]',
        'avaliacoes_atendimento_regulares'      => 'integer|max_length[11]',
        'avaliacoes_atendimento_ruins'          => 'integer|max_length[11]',
        'solicitacoes'                          => 'integer|max_length[11]',
        'solicitacoes_atendidas'                => 'integer|max_length[11]',
        'solicitacoes_nao_atendidas'            => 'integer|max_length[11]',
        'observacoes'                           => 'string',
    ];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = [];
	protected $afterInsert          = [];
	protected $beforeUpdate         = [];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];
}
