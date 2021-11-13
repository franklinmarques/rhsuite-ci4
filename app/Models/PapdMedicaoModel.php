<?php

namespace App\Models;

use App\Entities\PapdMedicao;

class PapdMedicaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'papd_medicoes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = PapdMedicao::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'ano',
        'mes',
        'total_pacientes_cadastrados',
        'total_pacientes_inativos',
        'total_pacientes_monitorados',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_empresa'                    => 'required|is_natural_no_zero|max_length[11]',
        'ano'                           => 'required|int|max_length[4]',
        'mes'                           => 'required|integer|max_length[2]',
        'total_pacientes_cadastrados'   => 'required|integer|max_length[11]',
        'total_pacientes_inativos'      => 'required|integer|max_length[11]',
        'total_pacientes_monitorados'   => 'required|integer|max_length[11]',
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
