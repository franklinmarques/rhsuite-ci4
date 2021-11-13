<?php

namespace App\Models;

use App\Entities\RequisicaoPessoalAprovador;

class RequisicaoPessoalAprovadorModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'requisicoes_pessoal_aprovadores';
	protected $primaryKey           = 'id_usuario';
	protected $useAutoIncrement     = false;
	protected $insertID             = 0;
	protected $returnType           = RequisicaoPessoalAprovador::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
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
        'id_usuario' => 'required|integer|max_length[11]',
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
