<?php

namespace App\Models;

use App\Entities\RequisicaoPessoalFaltaAtraso;

class RequisicaoPessoalFaltaAtrasoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'requisicoes_pessoal_faltas_atrasos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = RequisicaoPessoalFaltaAtraso::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'id_depto',
        'ano',
        'mes',
        'total_faltas',
        'total_atrasos',
        'tempo_total_atraso',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_empresa'            => 'required|is_natural_no_zero|max_length[11]',
        'id_depto'              => 'required|is_natural_no_zero|max_length[11]',
        'ano'                   => 'required|int|max_length[4]',
        'mes'                   => 'required|integer|max_length[2]',
        'total_faltas'          => 'required|integer|max_length[11]',
        'total_atrasos'         => 'required|integer|max_length[11]',
        'tempo_total_atraso'    => 'valid_time',
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
