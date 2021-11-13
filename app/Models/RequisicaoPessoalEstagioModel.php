<?php

namespace App\Models;

use App\Entities\RequisicaoPessoalEstagio;

class RequisicaoPessoalEstagioModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'requisicoes_pessoal_estagios';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = RequisicaoPessoalEstagio::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'nome',
        'destino_email',
        'email_responsavel',
        'mensagem',
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
        'destino_email'     => 'required|string|max_length[255]',
        'email_responsavel' => 'required|string|max_length[255]',
        'mensagem'          => 'string',
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
