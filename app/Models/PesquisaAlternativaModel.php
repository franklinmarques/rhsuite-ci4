<?php

namespace App\Models;

use App\Entities\PesquisaAlternativa;

class PesquisaAlternativaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'pesquisa_alternativas';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = PesquisaAlternativa::class;
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
        'peso'          => 'required|integer|max_length[2]',
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
