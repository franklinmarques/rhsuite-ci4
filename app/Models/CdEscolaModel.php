<?php

namespace App\Models;

use App\Entities\CdEscola;

class CdEscolaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'cd_escolas';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = CdEscola::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'nome',
        'id_diretoria',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'municipio',
        'telefone',
        'telefone_contato',
        'email',
        'cep',
        'periodo_manha',
        'periodo_tarde',
        'periodo_noite',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'nome'              => 'required|string|max_length[100]',
        'id_diretoria'      => 'required|is_natural_no_zero|max_length[11]',
        'endereco'          => 'string|max_length[255]',
        'numero'            => 'integer|max_length[11]',
        'complemento'       => 'string|max_length[255]',
        'bairro'            => 'string|max_length[50]',
        'municipio'         => 'required|string|max_length[100]',
        'telefone'          => 'string|max_length[30]',
        'telefone_contato'  => 'string|max_length[30]',
        'email'             => 'string|max_length[255]',
        'cep'               => 'string|max_length[20]',
        'periodo_manha'     => 'integer|max_length[1]',
        'periodo_tarde'     => 'integer|max_length[1]',
        'periodo_noite'     => 'integer|max_length[1]',
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
