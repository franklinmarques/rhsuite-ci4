<?php

namespace App\Models;

use App\Entities\AnaliseSwotConsolidacao;

class AnaliseSwotConsolidacaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'analise_swot_consolidacoes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = AnaliseSwotConsolidacao::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_analise',
        'id_ambiente_externo',
        'id_ambiente_interno',
        'avaliacao',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_analise'            => 'required|is_natural_no_zero|max_length[11]',
        'id_ambiente_externo'   => 'required|is_natural_no_zero|max_length[11]',
        'id_ambiente_interno'   => 'required|is_natural_no_zero|max_length[11]',
        'avaliacao'             => 'required|string',
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
