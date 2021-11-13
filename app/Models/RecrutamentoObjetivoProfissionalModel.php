<?php

namespace App\Models;

use App\Entities\RecrutamentoObjetivoProfissional;

class RecrutamentoObjetivoProfissionalModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'recrutamento_objetivos_profissionais';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = RecrutamentoObjetivoProfissional::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_usuario',
        'objetivos',
        'areas_interesse',
        'pretensao_salarial',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_usuario'            => 'required|is_natural_no_zero|max_length[11]',
        'objetivos'             => 'required|string',
        'areas_interesse'       => 'required|string',
        'pretensao_salarial'    => 'numeric|max_length[10]',
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
