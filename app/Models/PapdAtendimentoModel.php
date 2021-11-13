<?php

namespace App\Models;

use App\Entities\PapdAtendimento;

class PapdAtendimentoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'papd_atendimentos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = PapdAtendimento::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_usuario',
        'id_paciente',
        'id_atividade',
        'data_atendimento',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_usuario'        => 'required|is_natural_no_zero|max_length[11]',
        'id_paciente'       => 'required|is_natural_no_zero|max_length[11]',
        'id_atividade'      => 'required|is_natural_no_zero|max_length[11]',
        'data_atendimento'  => 'required|valid_date',
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
