<?php

namespace App\Models;

use App\Entities\UsuarioExamePeriodico;

class UsuarioExamePeriodicoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'usuarios_exames_periodicos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = UsuarioExamePeriodico::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_usuario',
        'data_programada',
        'data_realizacao',
        'data_entrega',
        'data_entrega_copia',
        'local_exame',
        'observacoes',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_usuario'            => 'required|is_natural_no_zero|max_length[11]',
        'data_programada'       => 'required|valid_date',
        'data_realizacao'       => 'valid_date',
        'data_entrega'          => 'valid_date',
        'data_entrega_copia'    => 'valid_date',
        'local_exame'           => 'string|max_length[255]',
        'observacoes'           => 'string',
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
