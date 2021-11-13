<?php

namespace App\Models;

use App\Entities\AssessmentAlternativa;

class AssessmentAlternativaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'assessments_alternativas';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = AssessmentAlternativa::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_modelo',
        'id_pergunta',
        'alternativa',
        'peso',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_modelo'     => 'required|is_natural_no_zero|max_length[11]',
        'id_pergunta'   => 'is_natural_no_zero|max_length[11]',
        'alternativa'   => 'required|string',
        'peso'          => 'integer|max_length[2]',
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
