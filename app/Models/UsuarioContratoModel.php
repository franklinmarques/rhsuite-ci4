<?php

namespace App\Models;

use App\Entities\UsuarioContrato;

class UsuarioContratoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'usuarios_contratos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = UsuarioContrato::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_usuario',
        'data_assinatura',
        'id_depto',
        'id_area',
        'id_setor',
        'id_cargo',
        'id_funcao',
        'contrato',
        'valor_posto',
        'conversor_dia',
        'conversor_hora',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_usuario'        => 'required|is_natural_no_zero|max_length[11]',
        'data_assinatura'   => 'required|valid_date',
        'id_depto'          => 'required|integer|max_length[11]',
        'id_area'           => 'required|integer|max_length[11]',
        'id_setor'          => 'required|integer|max_length[11]',
        'id_cargo'          => 'required|integer|max_length[11]',
        'id_funcao'         => 'required|integer|max_length[11]',
        'contrato'          => 'string|max_length[255]',
        'valor_posto'       => 'required|numeric|max_length[10]',
        'conversor_dia'     => 'numeric|max_length[10]',
        'conversor_hora'    => 'numeric|max_length[10]',
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
