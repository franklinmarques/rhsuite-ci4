<?php

namespace App\Models;

use App\Entities\CandidatoVaga;

class CandidatoVagaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'candidatos_vagas';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = CandidatoVaga::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_candidato',
        'codigo_vaga',
        'data_cadastro',
        'status',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_candidato'  => 'required|is_natural_no_zero|max_length[11]',
        'codigo_vaga'   => 'required|is_natural_no_zero|max_length[11]',
        'data_cadastro' => 'required|valid_date',
        'status'        => 'string|max_length[1]',
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
