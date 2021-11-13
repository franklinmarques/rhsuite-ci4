<?php

namespace App\Models;

use App\Entities\Documento;

class DocumentoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'documentos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = Documento::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_usuario',
        'id_tipo',
        'id_colaborador',
        'data_cadastro',
        'descricao',
        'arquivo',
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
        'id_usuario'        => 'required|integer|max_length[11]',
        'id_tipo'           => 'integer|max_length[11]',
        'id_colaborador'    => 'integer|max_length[11]',
        'data_cadastro'     => 'required|valid_date',
        'descricao'         => 'required|string|max_length[200]',
        'arquivo'           => 'string',
        'observaoces'       => 'string|max_length[65535]',
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
