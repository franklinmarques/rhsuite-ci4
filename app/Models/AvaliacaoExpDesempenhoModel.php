<?php

namespace App\Models;

use App\Entities\AvaliacaoExpDesempenho;

class AvaliacaoExpDesempenhoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'avaliacao_exp_desempenhos';
	protected $primaryKey           = 'id_avaliador';
	protected $useAutoIncrement     = false;
	protected $insertID             = 0;
	protected $returnType           = AvaliacaoExpDesempenho::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_avaliador',
        'pontos_fortes',
        'pontos_fracos',
        'observacoes',
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
        'id_avaliador'  => 'required|integer|max_length[11]',
        'pontos_fortes' => 'string',
        'pontos_fracos' => 'string',
        'observacoes'   => 'string',
        'data'          => 'valid_date',
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
