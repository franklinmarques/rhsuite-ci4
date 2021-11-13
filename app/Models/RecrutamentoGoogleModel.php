<?php

namespace App\Models;

use App\Entities\RecrutamentoGoogle;

class RecrutamentoGoogleModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'recrutamento_google';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = RecrutamentoGoogle::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'cliente',
        'cargo',
        'cidade',
        'nome',
        'data_nascimento',
        'deficiencia',
        'telefone',
        'email',
        'fonte_contratacao',
        'status',
        'data_entrevista_rh',
        'resultado_entrevista_rh',
        'data_entrevista_cliente',
        'resultado_entrevista_cliente',
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
        'cliente'                       => 'string|max_length[255]',
        'cargo'                         => 'string|max_length[200]',
        'cidade'                        => 'string|max_length[200]',
        'nome'                          => 'required|string|max_length[255]',
        'data_nascimento'               => 'valid_date',
        'deficiencia'                   => 'string|max_length[255]',
        'telefone'                      => 'string|max_length[255]',
        'email'                         => 'string|max_length[255]',
        'fonte_contratacao'             => 'string|max_length[200]',
        'status'                        => 'string|max_length[200]',
        'data_entrevista_rh'            => 'string|max_length[200]',
        'resultado_entrevista_rh'       => 'string|max_length[200]',
        'data_entrevista_cliente'       => 'string|max_length[200]',
        'resultado_entrevista_cliente'  => 'string|max_length[200]',
        'observacoes'                   => 'string',
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
