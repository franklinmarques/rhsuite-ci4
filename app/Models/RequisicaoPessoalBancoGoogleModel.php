<?php

namespace App\Models;

use App\Entities\RequisicaoPessoalBancoGoogle;

class RequisicaoPessoalBancoGoogleModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'requisicoes_pessoal_banco_google';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = RequisicaoPessoalBancoGoogle::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_requisicao',
        'cliente',
        'nome_candidato',
        'cargo',
        'cidade',
        'deficiencia',
        'telefone',
        'fonte_contratacao',
        'data_captacao',
        'data_entrevista_rh',
        'resultado-entrevista_rh',
        'data_entrevista_cliente',
        'resultado_entrevista_cliente',
        'status',
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
        'id_requisicao'                 => 'required|is_natural_no_zero|max_length[11]',
        'cliente'                       => 'required|integer|max_length[11]',
        'nome_candidato'                => 'required|string|max_length[255]',
        'cargo'                         => 'required|integer|max_length[11]',
        'cidade'                        => 'required|integer|max_length[11]',
        'deficiencia'                   => 'integer|max_length[11]',
        'telefone'                      => 'string|max_length[50]',
        'fonte_contratacao'             => 'integer|max_length[11]',
        'data_captacao'                 => 'valid_date',
        'data_entrevista_rh'            => 'valid_date',
        'resultado-entrevista_rh'       => 'integer|max_length[11]',
        'data_entrevista_cliente'       => 'valid_date',
        'resultado_entrevista_cliente'  => 'integer|max_length[11]',
        'status'                        => 'integer|max_length[11]',
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
