<?php

namespace App\Models;

use App\Entities\IcomSpLembrete;

class IcomSpLembreteModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'icom_sp_lembretes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = IcomSpLembrete::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'data',
        'titulo',
        'tipo',
        'conteudo_texto',
        'arquivo_video',
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
        'id_empresa'        => 'required|is_natural_no_zero|max_length[11]',
        'data'              => 'required|valid_date',
        'titulo'            => 'required|string|max_length[255]',
        'tipo'              => 'required|string|max_length[1]',
        'conteudo_texto'    => 'string',
        'arquivo_video'     => 'string|max_length[255]',
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
    ];
}
