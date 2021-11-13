<?php

namespace App\Models;

use App\Entities\MensagemEnviada;

class MensagemEnviadaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'mensagens_enviadas';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = MensagemEnviada::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_remetente',
        'id_destinatario',
        'titulo',
        'mensagem',
        'anexo',
        'data_cadastro',
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
        'id_remetente'      => 'required|integer|max_length[11]',
        'id_destinatario'   => 'required|integer|max_length[11]',
        'titulo'            => 'string',
        'mensagem'          => 'required|string',
        'anexo'             => 'string',
        'data_cadastro'     => 'required|valid_date',
        'status'            => 'required|integer|max_length[2]',
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
