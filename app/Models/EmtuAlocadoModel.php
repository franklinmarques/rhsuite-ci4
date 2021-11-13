<?php

namespace App\Models;

use App\Entities\EmtuAlocado;

class EmtuAlocadoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'emtu_alocados';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EmtuAlocado::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_alocacao',
        'id_usuario',
        'nome_usuario',
        'id_funcao',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_alocacao'   => 'required|is_natural_no_zero|max_length[11]',
        'id_usuario'    => 'is_natural_no_zero|max_length[11]',
        'nome_usuario'  => 'required|string|max_length[255]',
        'id_funcao'     => 'is_natural_no_zero|max_length[11]',
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
