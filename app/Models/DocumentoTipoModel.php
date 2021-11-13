<?php

namespace App\Models;

use App\Entities\DocumentoTipo;

class DocumentoTipoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'documentos_tipos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = DocumentoTipo::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_usuario',
        'data_cadastro',
        'descricao',
        'categoria',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_usuario'    => 'required|integer|max_length[11]',
        'data_cadastro' => 'required|valid_date',
        'descricao'     => 'required|string|max_length[200]',
        'categoria'     => 'integer|max_length[11]',
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

    public const CATEGORIAS = [
        '1' => 'Colaborador',
        '2' => 'Organização',
    ];
}
