<?php

namespace App\Models;

use App\Entities\AvaliacaoExpPergunta;

class AvaliacaoExpPerguntaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'avaliacao_exp_perguntas';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = AvaliacaoExpPergunta::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_modelo',
        'pergunta',
        'tipo',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_modelo' => 'required|is_natural_no_zero|max_length[11]',
        'pergunta'  => 'required|string',
        'tipo'      => 'required|string|max_length[1]',
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
