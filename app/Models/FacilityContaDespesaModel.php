<?php

namespace App\Models;

use App\Entities\FacilityContaDespesa;

class FacilityContaDespesaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'facilities_contas_despesas';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = FacilityContaDespesa::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_item',
        'nome',
        'valor',
        'data_vencimento',
        'mes',
        'ano',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_item'           => 'required|is_natural_no_zero|max_length[11]',
        'nome'              => 'required|string|max_length[255]',
        'valor'             => 'required|numeric|max_length[10]',
        'data_vencimento'   => 'required|valid_date',
        'mes'               => 'required|integer|max_length[2]',
        'ano'               => 'required|int|max_length[4]',
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
