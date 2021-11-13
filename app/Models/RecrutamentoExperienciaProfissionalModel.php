<?php

namespace App\Models;

use App\Entities\RecrutamentoExperienciaProfissional;

class RecrutamentoExperienciaProfissionalModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'recrutamento_experiencias_profissionais';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = RecrutamentoExperienciaProfissional::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_usuario',
        'instituicao',
        'data_entrada',
        'data_saida',
        'cargo_entrada',
        'cargo_saida',
        'salario_entrada',
        'salario_saida',
        'motivo_saida',
        'realizacoes',
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
        'instituicao'       => 'required|string|max_length[255]',
        'data_entrada'      => 'required|valid_date',
        'data_saida'        => 'valid_date',
        'cargo_entrada'     => 'required|string|max_length[255]',
        'cargo_saida'       => 'string|max_length[255]',
        'salario_entrada'   => 'numeric|max_length[10]',
        'salario_saida'     => 'numeric|max_length[10]',
        'motivo_saida'      => 'string|max_length[255]',
        'realizacoes'       => 'string',
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
