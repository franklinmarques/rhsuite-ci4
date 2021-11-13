<?php

namespace App\Models;

use App\Entities\CargoDimensao;

class CargoDimensaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'cargos_dimensoes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = CargoDimensao::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'nome',
        'cargo_competencia',
        'nivel',
        'peso',
        'atitude',
        'id_dimensao',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'nome'              => 'required|string|max_length[255]',
        'cargo_competencia' => 'required|is_natural_no_zero|max_length[11]',
        'nivel'             => 'required|integer|max_length[11]',
        'peso'              => 'required|integer|max_length[11]',
        'atitude'           => 'required|integer|max_length[11]',
        'id_dimensao'       => 'is_natural_no_zero|max_length[11]',
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
