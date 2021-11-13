<?php

namespace App\Models;

use App\Entities\CompraFornecedorPrestador;

class CompraFornecedorPrestadorModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'compras_fornecedores_prestadores';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = CompraFornecedorPrestador::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'nome',
        'tipo',
        'id_subtipo',
        'vinculo',
        'pessoa_contato',
        'telefone',
        'email',
        'status',
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
        'nome'              => 'required|string|max_length[255]',
        'tipo'              => 'required|integer|exact_length[1]',
        'id_subtipo'        => 'required|is_natural_no_zero|max_length[1]',
        'vinculo'           => 'integer|exact_length[1]',
        'pessoa_contato'    => 'string|max_length[255]',
        'telefone'          => 'string|max_length[255]',
        'email'             => 'string|max_length[255]',
        'status'            => 'required|integer|exact_length[1]',
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
        '1' => 'Produto',
        '2' => 'Serviço',
    ];
    public const VINCULOS = [
        '1' => 'Contrato mensal',
        '2' => 'Contratação esporádica',
    ];
    public const STATUS = [
        '1' => 'Ativo',
        '2' => 'Inativo',
    ];
}
