<?php

namespace App\Models;

use App\Entities\StAlocacao;

class StAlocacaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'st_alocacoes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = StAlocacao::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'data',
        'depto',
        'area',
        'setor',
        'ano',
        'mes',
        'dia_fechamento',
        'contrato',
        'descricao_servico',
        'valor_servico',
        'qtde_alocados_potenciais',
        'qtde_alocados_ativos',
        'turnover_reposicao',
        'turnover_aumento_quadro',
        'turnover_desligamento_empresa',
        'turnover_desligamento_colaborador',
        'observacoes',
        'valor_projetado',
        'valor_realizado',
        'total_faltas',
        'total_dias_cobertos',
        'total_dias_descobertos',
        'mes_bloqueado',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_empresa'                        => 'required|is_natural_no_zero|max_length[11]',
        'data'                              => 'required|valid_date',
        'depto'                             => 'required|string|max_length[255]',
        'area'                              => 'required|string|max_length[255]',
        'setor'                             => 'required|string|max_length[255]',
        'ano'                               => 'required|int|max_length[4]',
        'mes'                               => 'required|integer|exact_length[2]',
        'dia_fechamento'                    => 'integer|max_length[2]',
        'contrato'                          => 'string|max_length[255]',
        'descricao_servico'                 => 'string|max_length[255]',
        'valor_servico'                     => 'numeric|max_length[10]',
        'qtde_alocados_potenciais'          => 'integer|max_length[11]',
        'qtde_alocados_ativos'              => 'integer|max_length[11]',
        'turnover_reposicao'                => 'integer|max_length[11]',
        'turnover_aumento_quadro'           => 'integer|max_length[11]',
        'turnover_desligamento_empresa'     => 'integer|max_length[11]',
        'turnover_desligamento_colaborador' => 'integer|max_length[11]',
        'observacoes'                       => 'string',
        'valor_projetado'                   => 'numeric|max_length[10]',
        'valor_realizado'                   => 'numeric|max_length[10]',
        'total_faltas'                      => 'required|numeric|max_length[10]',
        'total_dias_cobertos'               => 'required|numeric|max_length[10]',
        'total_dias_descobertos'            => 'required|numeric|max_length[10]',
        'mes_bloqueado'                     => 'integer|exact_length[1]',
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
