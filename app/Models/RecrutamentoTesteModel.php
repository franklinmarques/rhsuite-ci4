<?php

namespace App\Models;

use App\Entities\RecrutamentoTeste;

class RecrutamentoTesteModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'recrutamento_testes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = RecrutamentoTeste::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_candidato',
        'id_modelo',
        'data_inicio',
        'data_termino',
        'minutos_duracao',
        'aleatorizacao',
        'data_acesso',
        'data_envio',
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
        'id_candidato'      => 'required|is_natural_no_zero|max_length[11]',
        'id_modelo'         => 'required|is_natural_no_zero|max_length[11]',
        'data_inicio'       => 'required|valid_date',
        'data_termino'      => 'required|valid_date',
        'minutos_duracao'   => 'integer|max_length[11]',
        'aleatorizacao'     => 'string|max_length[1]',
        'data_acesso'       => 'valid_date',
        'data_envio'        => 'valid_date',
        'status'            => 'string|max_length[1]',
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
