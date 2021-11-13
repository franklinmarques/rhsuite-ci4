<?php

namespace App\Models;

use App\Entities\UsuarioIntegracao;

class UsuarioIntegracaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'usuarios_integracoes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = UsuarioIntegracao::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_usuario',
        'data_inicio',
        'data_termino',
        'atividades_desenvolvidas',
        'realizadores',
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
        'id_usuario'                => 'required|is_natural_no_zero|max_length[11]',
        'data_inicio'               => 'required|valid_date',
        'data_termino'              => 'required|valid_date',
        'atividades_desenvolvidas'  => 'required|string',
        'realizadores'              => 'required|string|max_length[255]',
        'observacoes'               => 'string',
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
