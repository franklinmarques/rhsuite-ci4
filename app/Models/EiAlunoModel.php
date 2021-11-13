<?php

namespace App\Models;

use App\Entities\EiAluno;

class EiAlunoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_alunos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiAluno::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'nome',
        'id_escola',
        'endereco',
        'numero',
        'complemento',
        'municipio',
        'telefone',
        'contato',
        'email',
        'cep',
        'hipotese_diagnostica',
        'nome_responsavel',
        'observacoes',
        'data_matricula',
        'data_afastamento',
        'data_desligamento',
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
        'nome'                  => 'required|string|max_length[100]',
        'id_escola'             => 'is_natural_no_zero|max_length[11]',
        'endereco'              => 'string|max_length[255]',
        'numero'                => 'integer|max_length[11]',
        'complemento'           => 'string|max_length[255]',
        'municipio'             => 'string|max_length[100]',
        'telefone'              => 'string|max_length[50]',
        'contato'               => 'string|max_length[255]',
        'email'                 => 'string|max_length[255]',
        'cep'                   => 'string|max_length[20]',
        'hipotese_diagnostica'  => 'string|max_length[255]',
        'nome_responsavel'      => 'string|max_length[100]',
        'observacoes'           => 'string',
        'data_matricula'        => 'valid_date',
        'data_afastamento'      => 'valid_date',
        'data_desligamento'     => 'valid_date',
        'status'                => 'required|string|max_length[1]',
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
