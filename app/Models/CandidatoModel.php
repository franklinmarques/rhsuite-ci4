<?php

namespace App\Models;

use App\Entities\Candidato;

class CandidatoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'candidatos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = Candidato::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'nome',
        'data_nascimento',
        'sexo',
        'estado_civil',
        'nome_mae',
        'nome_pai',
        'cpf',
        'rg',
        'pis',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'escolaridade',
        'deficiencia',
        'foto',
        'telefone',
        'email',
        'senha',
        'token',
        'data_inscricao',
        'fonte_contratacao',
        'data_edicao',
        'nivel_acesso',
        'url',
        'arquivo_curriculo',
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
        'id_empresa'        => 'required|is_natural_no_zero|max_length[11]',
        'nome'              => 'required|string|max_length[255]',
        'data_nascimento'   => 'valid_date',
        'sexo'              => 'in_list[M,F]',
        'estado_civil'      => 'integer|max_length[11]',
        'nome_mae'          => 'string|max_length[255]',
        'nome_pai'          => 'string|max_length[255]',
        'cpf'               => 'string|is_unique[candidatos.cpf,id,{id}]|max_length[14]',
        'rg'                => 'string|max_length[13]',
        'pis'               => 'string|max_length[14]',
        'logradouro'        => 'string|max_length[255]',
        'numero'            => 'integer|max_length[11]',
        'complemento'       => 'string|max_length[255]',
        'bairro'            => 'string|max_length[50]',
        'cidade'            => 'is_natural_no_zero|max_length[11]',
        'estado'            => 'is_natural_no_zero|max_length[2]',
        'cep'               => 'string|max_length[9]',
        'escolaridade'      => 'is_natural_no_zero|max_length[11]',
        'deficiencia'       => 'is_natural_no_zero|max_length[11]',
        'foto'              => 'string|max_length[255]',
        'telefone'          => 'required|string|max_length[255]',
        'email'             => 'required|string|is_unique[candidatos.email,id,{id}]|max_length[255]',
        'senha'             => 'required|string|max_length[32]',
        'token'             => 'required|string|max_length[255]',
        'data_inscricao'    => 'valid_date',
        'fonte_contratacao' => 'string|max_length[30]',
        'data_edicao'       => 'valid_date',
        'nivel_acesso'      => 'required|string|max_length[1]',
        'url'               => 'string|max_length[255]',
        'arquivo_curriculo' => 'string|max_length[255]',
        'status'            => 'required|string|max_length[1]',
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
