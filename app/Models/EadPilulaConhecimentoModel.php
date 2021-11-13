<?php

namespace App\Models;

use App\Entities\EadPilulaConhecimento;

class EadPilulaConhecimentoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ead_pilulas_conhecimento';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EadPilulaConhecimento::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'id_curso',
        'id_pilula_conhecimento_area',
        'publico',
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
        'id_curso'                      => 'required|is_natural_no_zero|max_length[11]',
        'id_pilula_conhecimento_area'   => 'is_natural_no_zero|max_length[11]',
        'publico'                       => 'required|integer|exact_length[1]',
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
