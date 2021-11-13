<?php

namespace App\Models;

use App\Entities\EiMedicaoMensal;

class EiMedicaoMensalModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_medicoes_mensais';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiMedicaoMensal::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'ano',
        'semestre',
        'mes',
        'depto',
        'id_diretoria',
        'total_escolas',
        'total_alunos',
        'total_cuidadores',
        'observacoes',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'ano'               => 'required|int|max_length[4]',
        'semestre'          => 'required|integer|exact_length[1]',
        'mes'               => 'required|integer|max_length[2]',
        'depto'             => 'string|max_length[255]',
        'id_diretoria'      => 'is_natural_no_zero|max_length[11]',
        'total_escolas'     => 'required|integer|max_length[11]',
        'total_alunos'      => 'required|integer|max_length[11]',
        'total_cuidadores'  => 'required|integer|max_length[11]',
        'observacoes'       => 'string',
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
