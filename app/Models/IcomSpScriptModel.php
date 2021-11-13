<?php

namespace App\Models;

use App\Entities\IcomSpScript;

class IcomSpScriptModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'icom_sp_scripts';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = IcomSpScript::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_tipo',
        'data',
        'formato',
        'titulo',
        'conteudo',
        'arquivo_video',
        'arquivo_pdf',
        'palavras_chave',
        'ativo',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_tipo'           => 'required|is_natural_no_zero|max_length[11]',
        'data'              => 'required|valid_date',
        'formato'           => 'string|max_length[1]',
        'titulo'            => 'required|string|max_length[50]',
        'conteudo'          => 'string',
        'arquivo_video'     => 'string|max_length[255]',
        'arquivo_pdf'       => 'string|max_length[255]',
        'palavras_chave'    => 'string',
        'ativo'             => 'integer|exact_length[1]',
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

    //--------------------------------------------------------------------

    protected $uploadConfig = [
        'arquivo_video' => ['upload_path' => './arquivos/icom/videos/', 'allowed_types' => '*'],
        'arquivo_pdf' => ['upload_path' => './arquivos/icom/pdf/', 'allowed_types' => '*'],
    ];
}
