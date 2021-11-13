<?php

namespace App\Models;

use App\Entities\Abcbr304PaginaVisitada;

class Abcbr304PaginaVisitadaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'abcbr304_paginas_visitadas';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = Abcbr304PaginaVisitada::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'id_usuario',
        'nome_usuario',
        'tipo_usuario',
        'url_pagina',
        'data_hora_acesso',
        'data_hora_atualizacao',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_empresa'            => 'integer|max_length[11]',
        'id_usuario'            => 'required|integer|max_length[11]',
        'nome_usuario'          => 'required|string|max_length[255]',
        'tipo_usuario'          => 'required|string|max_length[255]',
        'url_pagina'            => 'required|string|max_length[255]',
        'data_hora_acesso'      => 'required|valid_date',
        'data_hora_atualizacao' => 'valid_date',
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
