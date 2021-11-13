<?php

namespace App\Models;

use App\Entities\AvaliacaoExpAvaliado;

class AvaliacaoExpAvaliadoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'avaliacao_exp_avaliados';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = AvaliacaoExpAvaliado::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_modelo',
        'id_avaliado',
        'id_supervisor',
        'data_atividades',
        'nota_corte',
        'observacoes',
        'id_avaliacao',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_modelo'         => 'required|is_natural_no_zero|max_length[11]',
        'id_avaliado'       => 'required|is_natural_no_zero|max_length[11]',
        'id_supervisor'     => 'is_natural_no_zero|max_length[11]',
        'data_atividades'   => 'required|valid_date',
        'nota_corte'        => 'required|integer|max_length[2]',
        'observacoes'       => 'string',
        'id_avaliacao'      => 'is_natural_no_zero|max_length[11]',
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
