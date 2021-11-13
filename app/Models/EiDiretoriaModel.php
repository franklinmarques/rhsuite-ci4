<?php

namespace App\Models;

use App\Entities\EiDiretoria;
use App\Models\Traits\FileUploadTrait;

class EiDiretoriaModel extends AbstractModel
{
    use FileUploadTrait;

	protected $DBGroup              = 'default';
	protected $table                = 'ei_diretorias';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiDiretoria::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'nome',
        'alias',
        'id_empresa',
        'depto',
        'municipio',
        'telefone',
        'id_coordenador',
        'nome_supervisor',
        'email_supervisor',
        'nome_coordenador',
        'email_coordenador',
        'nome_administrativo',
        'email_administrativo',
        'depto_cliente',
        'cargo_coordenador',
        'cargo_supervisor',
        'assinatura_digital_coordenador',
        'senha_exclusao',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'nome'                              => 'required|string|max_length[100]',
        'alias'                             => 'string|max_length[100]',
        'id_empresa'                        => 'required|is_natural_no_zero|max_length[11]',
        'depto'                             => 'required|string|max_length[255]',
        'municipio'                         => 'required|string|max_length[100]',
        'telefone'                          => 'string|max_length[30]',
        'id_coordenador'                    => 'is_natural_no_zero|max_length[11]',
        'nome_supervisor'                   => 'string|max_length[255]',
        'email_supervisor'                  => 'string|max_length[255]',
        'nome_coordenador'                  => 'string|max_length[255]',
        'email_coordenador'                 => 'string|max_length[255]',
        'nome_administrativo'               => 'string|max_length[255]',
        'email_administrativo'              => 'string|max_length[255]',
        'depto_cliente'                     => 'string|max_length[255]',
        'cargo_coordenador'                 => 'string|max_length[255]',
        'cargo_supervisor'                  => 'string|max_length[255]',
        'assinatura_digital_coordenador'    => 'string|max_length[255]',
        'senha_exclusao'                    => 'string|max_length[255]',
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

    protected $filePaths = ['assinatura_digital_coordenador' => './arquivos/ei/assinatura_digital/'];
    protected $fileAllowedTypes = ['assinatura_digital_coordenador' => 'gif|jpg|jpeg|png'];
}
