<?php

namespace App\Models;

use App\Entities\PesquisaJungCaracteristica;

class PesquisaJungCaracteristicaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'pesquisa_jung_caracteristicas';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = PesquisaJungCaracteristica::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'tipo_comportamental',
        'nome',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_empresa'            => 'required|is_natural_no_zero|max_length[11]',
        'tipo_comportamental'   => 'required|string|max_length[1]',
        'nome'                  => 'required|string|max_length[100]',
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
