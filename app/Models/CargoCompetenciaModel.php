<?php

namespace App\Models;

use App\Entities\CargoCompetencia;

class CargoCompetenciaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'cargos_competencias';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = CargoCompetencia::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'nome',
        'id_cargo',
        'tipo_competencia',
        'peso',
        'id_modelo',
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
        'id_cargo'          => 'required|is_natural_no_zero|max_length[11]',
        'tipo_competencia'  => 'required|string|max_length[1]',
        'peso'              => 'required|integer|max_length[11]',
        'id_modelo'         => 'is_natural_no_zero|max_length[11]',
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
