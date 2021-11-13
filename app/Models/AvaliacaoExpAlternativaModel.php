<?php

namespace App\Models;

use App\Entities\AvaliacaoExpAlternativa;

class AvaliacaoExpAlternativaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'avaliacao_exp_alternativas';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = AvaliacaoExpAlternativa::class;
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
        'alternativa'   => 'required|string|max_length[255]',
        'peso'          => 'required|integer|max_length[3]',
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
