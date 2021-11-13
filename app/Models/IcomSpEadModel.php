<?php

namespace App\Models;

use App\Entities\IcomSpEad;

class IcomSpEadModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'icom_sp_ead';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = IcomSpEad::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'titulo',
        'descricao',
        'tipo_arquivo',
        'arquivo_pdf',
        'arquivo_video',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_empresa'    => 'required|is_natural_no_zero|max_length[11]',
        'titulo'        => 'required|string|max_length[255]',
        'descricao'     => 'string',
        'tipo_arquivo'  => 'required|string|max_length[1]',
        'arquivo_pdf'   => 'string|max_length[255]',
        'arquivo_video' => 'string|max_length[255]',
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
        'arquivo_pdf' => ['upload_path' => './arquivos/icom/pdf/', 'allowed_types' => '*'],
        'arquivo_video' => ['upload_path' => './arquivos/icom/videos/', 'allowed_types' => '*'],
    ];
}
