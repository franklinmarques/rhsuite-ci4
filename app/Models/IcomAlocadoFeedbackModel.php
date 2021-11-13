<?php

namespace App\Models;

use App\Entities\IcomAlocadoFeedback;

class IcomAlocadoFeedbackModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'icom_alocados_feedbacks';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = IcomAlocadoFeedback::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_usuario',
        'id_alocacao',
        'id_alocado',
        'id_usuario_orientador',
        'nome_usuario_orientador',
        'data',
        'descricao',
        'avaliacao_desempenho',
        'plano_desenvolimento_melhorias',
        'resultado',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_usuario'                        => 'integer|max_length[11]',
        'id_alocacao'                       => 'is_natural_no_zero|max_length[11]',
        'id_alocado'                        => 'integer|max_length[11]',
        'id_usuario_orientador'             => 'is_natural_no_zero|max_length[11]',
        'nome_usuario_orientador'           => 'required|string|max_length[255]',
        'data'                              => 'required|valid_date',
        'descricao'                         => 'string',
        'avaliacao_desempenho'              => 'string',
        'plano_desenvolimento_melhorias'    => 'string',
        'resultado'                         => 'string',
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
