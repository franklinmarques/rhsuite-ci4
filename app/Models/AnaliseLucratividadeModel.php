<?php

namespace App\Models;

use App\Entities\AnaliseLucratividade;

class AnaliseLucratividadeModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'analise_lucratividade';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = AnaliseLucratividade::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'nome',
        'data',
        'descricao',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_empresa'    => 'required|is_natural_no_zero|max_length[11]',
        'nome'          => 'required|string|max_length[255]',
        'data'          => 'required|valid_date',
        'descricao'     => 'string',
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
