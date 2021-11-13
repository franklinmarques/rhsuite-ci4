<?php

namespace App\Models;

use App\Entities\IcomSpTelefoneUtil;

class IcomSpTelefoneUtilModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'icom_sp_telefones_uteis';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = IcomSpTelefoneUtil::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_cliente',
        'id_servico',
        'descricao',
        'observacoes',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_cliente'    => 'required|is_natural_no_zero|max_length[11]',
        'id_servico'    => 'required|is_natural_no_zero|max_length[11]',
        'descricao'     => 'required|string|max_length[255]',
        'observacoes'   => 'string',
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
