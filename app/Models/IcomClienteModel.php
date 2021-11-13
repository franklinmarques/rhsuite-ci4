<?php

namespace App\Models;

use App\Entities\IcomCliente;

class IcomClienteModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'icom_clientes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = IcomCliente::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'nome',
        'nome_fantasia',
        'cnpj',
        'data_vencimento_contrato',
        'tipo',
        'centro_custo',
        'condicoes_pagamento',
        'valor_contratual_mensal',
        'valor_minutos_excedidos',
        'qtde_horas_contratadas',
        'endereco',
        'observacoes',
        'contato_principal',
        'telefone_contato_principal',
        'email_contato_principal',
        'cargo_contato_principal',
        'contato_secundario',
        'telefone_contato_secundario',
        'email_contato_secundario',
        'cargo_contato_secundario',
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
        'nome_fantasia'                 => 'string|max_length[255]',
        'cnpj'                          => 'string|max_length[18]',
        'data_vencimento_contrato'      => 'valid_date',
        'tipo'                          => 'string|max_length[1]',
        'centro_custo'                  => 'string|max_length[25]',
        'condicoes_pagamento'           => 'string|max_length[255]',
        'valor_contratual_mensal'       => 'numeric|max_length[10]',
        'valor_minutos_excedidos'       => 'numeric|max_length[10]',
        'qtde_horas_contratadas'        => 'numeric|max_length[10]',
        'endereco'                      => 'string|max_length[255]',
        'observacoes'                   => 'string',
        'contato_principal'             => 'string|max_length[255]',
        'telefone_contato_principal'    => 'string|max_length[255]',
        'email_contato_principal'       => 'string|max_length[255]',
        'cargo_contato_principal'       => 'string|max_length[255]',
        'contato_secundario'            => 'string|max_length[255]',
        'telefone_contato_secundario'   => 'string|max_length[255]',
        'email_contato_secundario'      => 'string|max_length[255]',
        'cargo_contato_secundario'      => 'string|max_length[255]',
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
        'I' => 'ICOM',
        'L' => 'Libras',
        'M' => 'ICOM + Libras',
    ];
}
