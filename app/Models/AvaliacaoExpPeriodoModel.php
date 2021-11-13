<?php

namespace App\Models;

use App\Entities\AvaliacaoExpPeriodo;

class AvaliacaoExpPeriodoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'avaliacao_exp_periodos';
	protected $primaryKey           = 'id_avaliado';
	protected $useAutoIncrement     = false;
	protected $insertID             = 0;
	protected $returnType           = AvaliacaoExpPeriodo::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_avaliado',
        'pontos_fortes',
        'pontos_fracos',
        'feedback1',
        'data_feedback1',
        'feedback2',
        'data_feedback2',
        'feedback3',
        'data_feedback3',
        'parecer_final',
        'data',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_avaliado'       => 'required|integer|max_length[11]',
        'pontos_fortes'     => 'string',
        'pontos_fracos'     => 'string',
        'feedback1'         => 'string',
        'data_feedback1'    => 'valid_date',
        'feedback2'         => 'string',
        'data_feedback2'    => 'valid_date',
        'feedback3'         => 'string',
        'data_feedback3'    => 'valid_date',
        'parecer_final'     => 'string|max_length[1]',
        'data'              => 'valid_date',
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
