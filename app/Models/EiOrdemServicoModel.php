<?php

namespace App\Models;

use App\Entities\EiOrdemServico;

class EiOrdemServicoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_ordens_servico';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiOrdemServico::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_contrato',
        'nome',
        'numero_empenho',
        'ano',
        'semestre',
        'escolas_nao_cadastradas',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_contrato'               => 'required|is_natural_no_zero|max_length[11]',
        'nome'                      => 'required|string|max_length[255]',
        'numero_empenho'            => 'string|max_length[255]',
        'ano'                       => 'required|int|max_length[4]',
        'semestre'                  => 'required|integer|exact_length[1]',
        'escolas_nao_cadastradas'   => 'string',
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
