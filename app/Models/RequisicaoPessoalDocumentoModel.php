<?php

namespace App\Models;

use App\Entities\RequisicaoPessoalDocumento;

class RequisicaoPessoalDocumentoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'requisicoes_pessoal_documentos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = RequisicaoPessoalDocumento::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_candidato',
        'nome_arquivo',
        'tipo_arquivo',
        'data_upload',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_candidato'  => 'required|is_natural_no_zero|max_length[11]',
        'nome_arquivo'  => 'required|string|max_length[255]',
        'tipo_arquivo'  => 'required|string|max_length[255]',
        'data_upload'   => 'required|valid_date',
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
