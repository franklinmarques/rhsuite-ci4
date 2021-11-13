<?php

namespace App\Models;

use App\Entities\EiLogPagamentoPrestador;

class EiLogPagamentoPrestadorModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_log_pagamento_prestador';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiLogPagamentoPrestador::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'data',
        'mes_faturamento',
        'id_usuario',
        'nome_usuario',
        'escola',
        'colaborador',
        'alunos',
        'observacoes',
        'quantidades',
        'valores',
        'valores_totais',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'data'              => 'required|valid_date',
        'mes_faturamento'   => 'integer|max_length[2]',
        'id_usuario'        => 'required|integer|max_length[11]',
        'nome_usuario'      => 'required|string|max_length[255]',
        'escola'            => 'required|string|max_length[255]',
        'colaborador'       => 'required|string|max_length[255]',
        'alunos'            => 'string',
        'observacoes'       => 'string',
        'quantidades'       => 'string',
        'valores'           => 'string',
        'valores_totais'    => 'string',
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
