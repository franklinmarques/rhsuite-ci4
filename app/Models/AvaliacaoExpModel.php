<?php

namespace App\Models;

use App\Entities\AvaliacaoExp;

class AvaliacaoExpModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'avaliacao_exp';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = AvaliacaoExp::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'nome',
        'id_modelo',
        'data_inicio',
        'data_termino',
        'ativo',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'nome'          => 'required|string|max_length[50]',
        'id_modelo'     => 'required|is_natural_no_zero|max_length[11]',
        'data_inicio'   => 'required|valid_date',
        'data_termino'  => 'required|valid_date',
        'ativo'         => 'required|integer|exact_length[1]',
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
