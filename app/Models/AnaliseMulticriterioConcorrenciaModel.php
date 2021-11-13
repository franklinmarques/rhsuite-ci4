<?php

namespace App\Models;

use App\Entities\AnaliseMulticriterioConcorrencia;

class AnaliseMulticriterioConcorrenciaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'analise_multicriterios_concorrencias';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = AnaliseMulticriterioConcorrencia::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_criterio',
        'id_concorrente',
        'desempenho',
        'resultado',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_criterio'       => 'required|is_natural_no_zero|max_length[11]',
        'id_concorrente'    => 'required|is_natural_no_zero|max_length[11]',
        'desempenho'        => 'required|integer|max_length[2]',
        'resultado'         => 'required|numeric|max_length[5]',
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
