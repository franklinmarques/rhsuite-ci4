<?php

namespace App\Models;

use App\Entities\EiFuncaoSupervisionada;

class EiFuncaoSupervisionadaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_funcoes_supervisionadas';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiFuncaoSupervisionada::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_supervisor',
        'cargo',
        'funcao',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_supervisor' => 'required|is_natural_no_zero|max_length[11]',
        'cargo'         => 'required|is_natural_no_zero|max_length[11]',
        'funcao'        => 'required|is_natural_no_zero|max_length[11]',
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
