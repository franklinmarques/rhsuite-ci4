<?php

namespace App\Models;

use App\Entities\EadAcesso;

class EadAcessoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ead_acessos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EadAcesso::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_curso_usuario',
        'id_pagina',
        'data_acesso',
        'data_atualizacao',
        'tempo_estudo',
        'data_finalizacao',
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
        'id_curso_usuario'  => 'required|is_natural_no_zero|max_length[11]',
        'id_pagina'         => 'required|is_natural_no_zero|max_length[11]',
        'data_acesso'       => 'required|valid_date',
        'data_atualizacao'  => 'valid_date',
        'tempo_estudo'      => 'valid_time',
        'data_finalizacao'  => 'valid_date',
        'status'            => 'required|integer|max_length[11]',
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
