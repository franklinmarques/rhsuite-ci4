<?php

namespace App\Models;

use App\Entities\CdApontamento;

class CdApontamentoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'cd_apontamentos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = CdApontamento::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_alocado',
        'data',
        'data_afastamento',
        'id_cuidador_sub',
        'status',
        'qtde_dias',
        'apontamento_asc',
        'apontamento_desc',
        'saldo',
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
        'id_alocado'        => 'required|is_natural_no_zero|max_length[11]',
        'data'              => 'required|valid_date',
        'data_afastamento'  => 'valid_date',
        'id_cuidador_sub'   => 'is_natural_no_zero|max_length[11]',
        'status'            => 'required|string|max_length[2]',
        'qtde_dias'         => 'integer|max_length[2]',
        'apontamento_asc'   => 'valid_time',
        'apontamento_desc'  => 'valid_time',
        'saldo'             => 'integer|max_length[11]',
        'observacoes'       => 'string',
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
