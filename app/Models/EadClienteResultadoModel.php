<?php

namespace App\Models;

use App\Entities\EadClienteResultado;

class EadClienteResultadoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ead_clientes_resultados';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EadClienteResultado::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_acesso',
        'id_questao',
        'id_alternativa',
        'valor',
        'resposta',
        'nota',
        'data_avaliacao',
        'status',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_acesso'         => 'required|is_natural_no_zero|max_length[11]',
        'id_questao'        => 'required|is_natural_no_zero|max_length[11]',
        'id_alternativa'    => 'is_natural_no_zero|max_length[11]',
        'valor'             => 'integer|max_length[11]',
        'resposta'          => 'string',
        'nota'              => 'integer|max_length[3]',
        'data_avaliacao'    => 'required|valid_date',
        'status'            => 'required|integer|max_length[11]',
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
