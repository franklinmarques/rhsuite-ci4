<?php

namespace App\Models;

use App\Entities\DocumentoTermo;

class DocumentoTermoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'documentos_termos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = DocumentoTermo::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'nome',
        'tipo',
        'arquivo',
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
        'nome'          => 'required|string|max_length[255]',
        'tipo'          => 'required|integer|max_length[1]',
        'arquivo'       => 'required|string|max_length[255]',
    ];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = ['setEmpresa'];
	protected $afterInsert          = [];
	protected $beforeUpdate         = ['setEmpresa'];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];

    //--------------------------------------------------------------------

    protected $uploadConfig = [
        'arquivo' => ['upload_path' => './arquivos/documentos/termos/', 'allowed_types' => 'pdf']
    ];

    public const TIPOS = [
        '1' => 'Termos de uso',
        '2' => 'Políticas de privacidade',
        '3' => 'Termos de consentimento',
    ];

    //--------------------------------------------------------------------

    protected function setEmpresa($data): array
    {
        if (empty($data['data'])) {
            return $data;
        }
        $data['data']['id_empresa'] = session('empresa');
        return $data;
    }
}
