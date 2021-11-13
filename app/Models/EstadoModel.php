<?php

namespace App\Models;

use App\Entities\Estado;

class EstadoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'estados';
	protected $primaryKey           = 'cod_uf';
	protected $useAutoIncrement     = false;
	protected $insertID             = 0;
	protected $returnType           = Estado::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'cod_uf',
        'estado',
        'uf',
        'cod_capital',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'cod_uf'        => 'required|integer|max_length[2]',
        'estado'        => 'required|string|max_length[30]',
        'uf'            => 'required|string|is_unique[estados.uf,cod_uf,{cod_uf}]|max_length[2]',
        'cod_capital'   => 'is_natural_no_zero|max_length[11]',
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
