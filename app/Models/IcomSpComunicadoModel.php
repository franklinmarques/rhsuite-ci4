<?php

namespace App\Models;

use App\Entities\IcomSpComunicado;

class IcomSpComunicadoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'icom_sp_comunicados';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = IcomSpComunicado::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_subcategoria',
        'data',
        'numero',
        'tipo',
        'titulo',
        'conteudo',
        'arquivo',
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
        'id_subcategoria'   => 'required|is_natural_no_zero|max_length[11]',
        'data'              => 'required|valid_date',
        'numero'            => 'required|string|max_length[10]',
        'tipo'              => 'string|max_length[1]',
        'titulo'            => 'required|string|max_length[50]',
        'conteudo'          => 'required|string',
        'arquivo'           => 'string|max_length[255]',
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

    protected $uploadConfig = ['arquivo' => ['upload_path' => './arquivos/icom/videos/', 'allowed_types' => '*']];
}
