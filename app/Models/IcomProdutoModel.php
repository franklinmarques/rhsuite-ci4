<?php

namespace App\Models;

use App\Entities\IcomProduto;

class IcomProdutoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'icom_produtos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = IcomProduto::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'id_setor',
        'codigo',
        'nome',
        'tipo',
        'dupla',
        'preco',
        'custo',
        'tipo_cobranca',
        'centro_custo',
        'complementos',
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
        'id_setor'      => 'required|integer|max_length[11]',
        'codigo'        => 'required|string|max_length[20]',
        'nome'          => 'required|string|max_length[255]',
        'tipo'          => 'required|string|max_length[1]',
        'dupla'         => 'integer|exact_length[1]',
        'preco'         => 'required|numeric|max_length[10]',
        'custo'         => 'numeric|max_length[10]',
        'tipo_cobranca' => 'required|string|max_length[1]',
        'centro_custo'  => 'string|max_length[255]',
        'complementos'  => 'string',
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

    public const TIPOS = [
        'P' => 'Produto',
        'S' => 'Serviço',
    ];
    public const TIPOS_COBRANCA = [
        'H' => 'Por hora',
        'M' => 'Por mês',
        'C' => 'Por colaborador/mês',
        'E' => 'Por entrega',
    ];
}
