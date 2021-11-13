<?php

namespace App\Models;

use App\Entities\EadUsuario;

class EadUsuarioModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ead_usuarios';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EadUsuario::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_usuario',
        'id_curso',
        'data_cadastro',
        'data_inicio',
        'data_maxima',
        'colaboradores_maximo',
        'nota_aprovacao',
        'tipo_treinamento',
        'local_treinamento',
        'nome',
        'carga_horaria_presencial',
        'avaliacao_presencial',
        'nome_fornecedor',
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
        'id_curso'                  => 'is_natural_no_zero|max_length[11]',
        'data_cadastro'             => 'required|valid_date',
        'data_inicio'               => 'valid_date',
        'data_maxima'               => 'valid_date',
        'colaboradores_maximo'      => 'integer|max_length[11]',
        'nota_aprovacao'            => 'integer|max_length[3]',
        'tipo_treinamento'          => 'string|max_length[1]',
        'local_treinamento'         => 'string|max_length[1]',
        'nome'                      => 'string|max_length[255]',
        'carga_horaria_presencial'  => 'valid_time',
        'avaliacao_presencial'      => 'integer|max_length[3]',
        'nome_fornecedor'           => 'string|max_length[255]',
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
