<?php

namespace App\Models;

use App\Entities\FacilityOrdemServico;

class FacilityOrdemServicoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'facilities_ordens_servico';
	protected $primaryKey           = 'numero_os';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = FacilityOrdemServico::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_usuario',
        'data_abertura',
        'data_resolucao_problema',
        'data_tratamento',
        'data_fechamento',
        'status',
        'prioridade',
        'id_requisitante',
        'id_depto',
        'id_area',
        'id_setor',
        'descricao_problema',
        'descricao_solicitacao',
        'complemento',
        'observacoes',
        'arquivo',
        'resolucao_satisfatoria',
        'observacoes_positivas',
        'observacoes_negativas',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_usuario'                => 'integer|max_length[11]',
        'data_abertura'             => 'required|valid_date',
        'data_resolucao_problema'   => 'valid_date',
        'data_tratamento'           => 'valid_date',
        'data_fechamento'           => 'valid_date',
        'status'                    => 'required|string|max_length[1]',
        'prioridade'                => 'required|integer|max_length[1]',
        'id_requisitante'           => 'required|is_natural_no_zero|max_length[11]',
        'id_depto'                  => 'is_natural_no_zero|max_length[11]',
        'id_area'                   => 'is_natural_no_zero|max_length[11]',
        'id_setor'                  => 'is_natural_no_zero|max_length[11]',
        'descricao_problema'        => 'string',
        'descricao_solicitacao'     => 'string',
        'complemento'               => 'string',
        'observacoes'               => 'string',
        'arquivo'                   => 'string|max_length[255]',
        'resolucao_satisfatoria'    => 'string|max_length[1]',
        'observacoes_positivas'     => 'string',
        'observacoes_negativas'     => 'string',
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
