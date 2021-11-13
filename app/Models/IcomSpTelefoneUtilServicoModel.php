<?php

namespace App\Models;

use App\Entities\IcomSpTelefoneUtilServico;

class IcomSpTelefoneUtilServicoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'icom_sp_telefones_uteis_servicos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = IcomSpTelefoneUtilServico::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_cliente',
        'nome',
        'telefone_1',
        'telefone_2',
        'url',
        'observacoes',
        'campo_extra',
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
        'nome'          => 'required|string|max_length[255]',
        'telefone_1'    => 'string|max_length[255]',
        'telefone_2'    => 'string|max_length[255]',
        'url'           => 'string|max_length[255]',
        'observacoes'   => 'string',
        'campo_extra'   => 'string|max_length[255]',
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
