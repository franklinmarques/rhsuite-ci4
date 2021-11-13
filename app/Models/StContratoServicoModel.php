<?php

namespace App\Models;

use App\Entities\StContratoServico;

class StContratoServicoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'st_contratos_servicos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = StContratoServico::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_contrato',
        'tipo',
        'descricao',
        'data_reajuste',
        'valor',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_contrato'   => 'required|is_natural_no_zero|max_length[11]',
        'tipo'          => 'required|integer|max_length[1]',
        'descricao'     => 'required|string|max_length[255]',
        'data_reajuste' => 'valid_date',
        'valor'         => 'required|numeric|max_length[10]',
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
