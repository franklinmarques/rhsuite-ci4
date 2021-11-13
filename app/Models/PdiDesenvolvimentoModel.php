<?php

namespace App\Models;

use App\Entities\PdiDesenvolvimento;

class PdiDesenvolvimentoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'pdi_desenvolvimento';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = PdiDesenvolvimento::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_pdi',
        'competencia',
        'descricao',
        'expectativa',
        'resultado',
        'data_inicio',
        'data_termino',
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
        'id_pdi'        => 'required|is_natural_no_zero|max_length[11]',
        'competencia'   => 'required|string|max_length[45]',
        'descricao'     => 'required|string',
        'expectativa'   => 'required|string',
        'resultado'     => 'required|string',
        'data_inicio'   => 'required|valid_date',
        'data_termino'  => 'required|valid_date',
        'status'        => 'string|max_length[1]',
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
