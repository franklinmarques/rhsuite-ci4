<?php

namespace App\Models;

use App\Entities\IcomSpContato;

class IcomSpContatoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'icom_sp_contatos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = IcomSpContato::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'data',
        'nome_responsavel',
        'nome_empresa',
        'telefone',
        'email',
        'motivo_ligacao',
        'agente_comercial',
        'possui_interesse',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_empresa'        => 'required|is_natural_no_zero|max_length[11]',
        'data'              => 'required|valid_date',
        'nome_responsavel'  => 'required|string|max_length[255]',
        'nome_empresa'      => 'required|string|max_length[255]',
        'telefone'          => 'string|max_length[255]',
        'email'             => 'required|string|max_length[255]',
        'motivo_ligacao'    => 'required|string',
        'agente_comercial'  => 'string|max_length[255]',
        'possui_interesse'  => 'required|integer|exact_length[1]',
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
