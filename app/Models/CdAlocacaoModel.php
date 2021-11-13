<?php

namespace App\Models;

use App\Entities\CdAlocacao;

class CdAlocacaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'cd_alocacoes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = CdAlocacao::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'data',
        'depto',
        'diretoria',
        'coordenador',
        'municipio',
        'supervisor',
        'total_faltas',
        'total_faltas_justificadas',
        'turnover_substituicao',
        'turnover_aumento_quadro',
        'turnover_desligamento_empresa',
        'turnover_desligamento_solicitacao',
        'intercorrencias_diretoria',
        'intercorrencias_cuidador',
        'intercorrencias_alunos',
        'acidentes_trabalho',
        'total_escolas',
        'total_alunos',
        'dias_letivos',
        'total_cuidadores',
        'total_cuidadores_cobrados',
        'total_cuidadores_ativos',
        'total_cuidadores_afastados',
        'total_supervisores',
        'total_supervisores_cobrados',
        'total_supervisores_ativos',
        'total_supervisores_afastados',
        'faturamento_projetado',
        'faturamento_realizado',
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
        'diretoria'                         => 'required|string|max_length[255]',
        'coordenador'                       => 'required|string|max_length[255]',
        'municipio'                         => 'required|string|max_length[255]',
        'supervisor'                        => 'required|string|max_length[255]',
        'total_faltas'                      => 'integer|max_length[2]',
        'total_faltas_justificadas'         => 'integer|max_length[2]',
        'turnover_substituicao'             => 'integer|max_length[11]',
        'turnover_aumento_quadro'           => 'integer|max_length[11]',
        'turnover_desligamento_empresa'     => 'integer|max_length[11]',
        'turnover_desligamento_solicitacao' => 'integer|max_length[11]',
        'intercorrencias_diretoria'         => 'integer|max_length[11]',
        'intercorrencias_cuidador'          => 'integer|max_length[11]',
        'intercorrencias_alunos'            => 'integer|max_length[11]',
        'acidentes_trabalho'                => 'integer|max_length[11]',
        'total_escolas'                     => 'integer|max_length[11]',
        'total_alunos'                      => 'integer|max_length[11]',
        'dias_letivos'                      => 'integer|max_length[2]',
        'total_cuidadores'                  => 'integer|max_length[11]',
        'total_cuidadores_cobrados'         => 'integer|max_length[11]',
        'total_cuidadores_ativos'           => 'integer|max_length[11]',
        'total_cuidadores_afastados'        => 'integer|max_length[11]',
        'total_supervisores'                => 'integer|max_length[11]',
        'total_supervisores_cobrados'       => 'integer|max_length[11]',
        'total_supervisores_ativos'         => 'integer|max_length[11]',
        'total_supervisores_afastados'      => 'integer|max_length[11]',
        'faturamento_projetado'             => 'numeric|max_length[10]',
        'faturamento_realizado'             => 'numeric|max_length[10]',
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
