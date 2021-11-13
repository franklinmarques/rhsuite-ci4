<?php

namespace App\Models;

use App\Entities\EmailAvaliacao;

class EmailAvaliacaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'emails_avaliacao';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EmailAvaliacao::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_avaliacao',
        'texto_inicio',
        'texto_cobranca',
        'texto_fim',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_avaliacao'      => 'required|integer|max_length[11]',
        'texto_inicio'      => 'required|string',
        'texto_cobranca'    => 'required|string',
        'texto_fim'         => 'required|string',
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
