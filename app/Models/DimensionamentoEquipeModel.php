<?php

namespace App\Models;

use App\Entities\DimensionamentoEquipe;

class DimensionamentoEquipeModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'dimensionamento_equipes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = DimensionamentoEquipe::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'id_depto',
        'id_area',
        'id_setor',
        'nome',
        'total_componentes',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_empresa'        => 'required|is_natural_no_zero|max_length[11]',
        'id_depto'          => 'is_natural_no_zero|max_length[11]',
        'id_area'           => 'is_natural_no_zero|max_length[11]',
        'id_setor'          => 'is_natural_no_zero|max_length[11]',
        'nome'              => 'required|string|max_length[255]',
        'total_componentes' => 'required|integer|max_length[11]',
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
