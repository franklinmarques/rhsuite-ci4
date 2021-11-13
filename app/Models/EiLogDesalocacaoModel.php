<?php

namespace App\Models;

use App\Entities\EiLogDesalocacao;

class EiLogDesalocacaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_log_desalocacao';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiLogDesalocacao::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'data',
        'id_usuario',
        'nome_usuario',
        'operacao',
        'nome_escola',
        'opcao',
        'id_alocado',
        'nome_cuidador',
        'periodo',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'data'          => 'required|valid_date',
        'id_usuario'    => 'required|integer|max_length[11]',
        'nome_usuario'  => 'required|string|max_length[255]',
        'operacao'      => 'required|string|max_length[255]',
        'nome_escola'   => 'required|string|max_length[255]',
        'opcao'         => 'required|string|max_length[6]',
        'id_alocado'    => 'required|integer|max_length[11]',
        'nome_cuidador' => 'required|string|max_length[255]',
        'periodo'       => 'required|string|max_length[9]',
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
