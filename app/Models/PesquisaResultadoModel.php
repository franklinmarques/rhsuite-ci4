<?php

namespace App\Models;

use App\Entities\PesquisaResultado;

class PesquisaResultadoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'pesquisa_resultados';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = PesquisaResultado::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_avaliador',
        'id_pergunta',
        'id_alternativa',
        'valor',
        'resposta',
        'data_avaliacao',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_avaliador'      => 'required|is_natural_no_zero|max_length[11]',
        'id_pergunta'       => 'required|is_natural_no_zero|max_length[11]',
        'id_alternativa'    => 'is_natural_no_zero|max_length[11]',
        'valor'             => 'integer|max_length[11]',
        'resposta'          => 'string',
        'data_avaliacao'    => 'required|valid_date',
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
