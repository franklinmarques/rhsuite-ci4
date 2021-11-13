<?php

namespace App\Models;

use App\Entities\PesquisaJungEstilo;

class PesquisaJungEstiloModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'pesquisa_jung_estilos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = PesquisaJungEstilo::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'nome',
        'laudo_comportamental_padrao',
        'perfil_preponderante',
        'atitude_primaria',
        'atitude_secundaria',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_empresa'                    => 'required|is_natural_no_zero|max_length[11]',
        'nome'                          => 'required|string|max_length[255]',
        'laudo_comportamental_padrao'   => 'string',
        'perfil_preponderante'          => 'required|string|max_length[1]',
        'atitude_primaria'              => 'required|string|max_length[1]',
        'atitude_secundaria'            => 'required|string|max_length[1]',
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
