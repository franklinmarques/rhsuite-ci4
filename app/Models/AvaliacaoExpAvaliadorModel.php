<?php

namespace App\Models;

use App\Entities\AvaliacaoExpAvaliador;

class AvaliacaoExpAvaliadorModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'avaliacao_exp_avaliadores';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = AvaliacaoExpAvaliador::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_avaliado',
        'id_avaliador',
        'data_avaliacao',
        'id_evento',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_avaliado'       => 'required|is_natural_no_zero|max_length[11]',
        'id_avaliador'      => 'required|is_natural_no_zero|max_length[11]',
        'data_avaliacao'    => 'required|valid_date',
        'id_evento'         => 'integer|max_length[11]',
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
