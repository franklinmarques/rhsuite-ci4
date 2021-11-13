<?php

namespace App\Models;

use App\Entities\EadPilulaConhecimentoColaborador;

class EadPilulaConhecimentoColaboradorModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ead_pilulas_conhecimento_colaboradores';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EadPilulaConhecimentoColaborador::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_pilula_conhecimento',
        'id_usuario',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_pilula_conhecimento'    => 'required|is_natural_no_zero|max_length[11]',
        'id_usuario'                => 'required|is_natural_no_zero|max_length[11]',
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
