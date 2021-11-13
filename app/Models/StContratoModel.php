<?php

namespace App\Models;

use App\Entities\StContrato;

class StContratoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'st_contratos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = StContrato::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'id_usuario',
        'nome',
        'depto',
        'area',
        'contrato',
        'data_assinatura',
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
        'id_usuario'        => 'is_natural_no_zero|max_length[11]',
        'nome'              => 'required|string|max_length[100]',
        'depto'             => 'required|string|max_length[255]',
        'area'              => 'required|string|max_length[255]',
        'contrato'          => 'required|string|max_length[255]',
        'data_assinatura'   => 'valid_date',
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
