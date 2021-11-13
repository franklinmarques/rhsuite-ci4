<?php

namespace App\Models;

use App\Entities\FacilityModelo;

class FacilityModeloModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'facilities_modelos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = FacilityModelo::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'id_facility_empresa',
        'nome',
        'tipo',
        'versao',
        'status',
        'id_copia',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_empresa'            => 'required|is_natural_no_zero|max_length[11]',
        'id_facility_empresa'   => 'required|is_natural_no_zero|max_length[11]',
        'nome'                  => 'required|string|max_length[255]',
        'tipo'                  => 'string|max_length[1]',
        'versao'                => 'required|string|max_length[255]',
        'status'                => 'required|integer|exact_length[1]',
        'id_copia'              => 'is_natural_no_zero|max_length[11]',
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
