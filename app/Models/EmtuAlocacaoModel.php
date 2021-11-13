<?php

namespace App\Models;

use App\Entities\EmtuAlocacao;

class EmtuAlocacaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'emtu_alocacoes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EmtuAlocacao::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'id_depto',
        'id_area',
        'id_setor',
        'mes',
        'ano',
        'dia_fechamento',
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
        'id_depto'          => 'is_natural_no_zero|max_length[11]',
        'id_area'           => 'is_natural_no_zero|max_length[11]',
        'id_setor'          => 'is_natural_no_zero|max_length[11]',
        'mes'               => 'required|integer|max_length[2]',
        'ano'               => 'required|int|max_length[4]',
        'dia_fechamento'    => 'integer|max_length[2]',
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
