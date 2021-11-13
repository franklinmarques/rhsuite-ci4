<?php

namespace App\Models;

use App\Entities\Abcbr304Processo;

class Abcbr304ProcessoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'abcbr304_processos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = Abcbr304Processo::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'id_menu',
        'url_pagina',
        'orientacoes_gerais',
        'nome_processo_1',
        'nome_processo_2',
        'arquivo_processo_1',
        'arquivo_processo_2',
        'nome_documentacao_1',
        'nome_documentacao_2',
        'arquivo_documentacao_1',
        'arquivo_documentacao_2',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_empresa'                => 'required|integer|max_length[11]',
        'id_menu'                   => 'integer|max_length[11]',
        'url_pagina'                => 'required|string|max_length[255]',
        'orientacoes_gerais'        => 'required|string',
        'nome_processo_1'           => 'string|max_length[30]',
        'nome_processo_2'           => 'string|max_length[30]',
        'arquivo_processo_1'        => 'string|max_length[255]',
        'arquivo_processo_2'        => 'string|max_length[255]',
        'nome_documentacao_1'       => 'string|max_length[30]',
        'nome_documentacao_2'       => 'string|max_length[30]',
        'arquivo_documentacao_1'    => 'string|max_length[255]',
        'arquivo_documentacao_2'    => 'string|max_length[255]',
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
