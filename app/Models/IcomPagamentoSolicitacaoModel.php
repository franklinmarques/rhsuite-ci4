<?php

namespace App\Models;

use App\Entities\IcomPagamentoSolicitacao;

class IcomPagamentoSolicitacaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'icom_pagamentos_solicitacoes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = IcomPagamentoSolicitacao::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_profissional_alocado',
        'nota_fiscal',
        'mes_referencia',
        'ano_referencia',
        'nome_solicitante',
        'cnpj',
        'centro_custo',
        'id_depto_prestador',
        'tipo_pagamento',
        'total_sessoes',
        'total_horas',
        'valor_total',
        'data_validacao',
        'assinatura_validador',
        'data_emissao',
        'assinatura',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_profissional_alocado'   => 'required|is_natural_no_zero|max_length[11]',
        'nota_fiscal'               => 'required|string|max_length[255]',
        'mes_referencia'            => 'required|integer|max_length[2]',
        'ano_referencia'            => 'required|int|max_length[4]',
        'nome_solicitante'          => 'string|max_length[255]',
        'cnpj'                      => 'string|max_length[18]',
        'centro_custo'              => 'string|max_length[255]',
        'id_depto_prestador'        => 'integer|max_length[11]',
        'tipo_pagamento'            => 'string|max_length[1]',
        'total_sessoes'             => 'integer|max_length[11]',
        'total_horas'               => 'numeric|max_length[10]',
        'valor_total'               => 'required|numeric|max_length[10]',
        'data_validacao'            => 'valid_date',
        'assinatura_validador'      => 'string|max_length[255]',
        'data_emissao'              => 'required|valid_date',
        'assinatura'                => 'string|max_length[255]',
    ];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = ['setDataEmissao'];
	protected $afterInsert          = [];
	protected $beforeUpdate         = [];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];

    //--------------------------------------------------------------------

    protected function setDataEmissao($data)
    {
        if (array_key_exists('data', $data) === false) {
            return $data;
        }

        $data['data']['data_emissao'] = date('Y-m-d H:i:s');

        return $data;
    }
}
