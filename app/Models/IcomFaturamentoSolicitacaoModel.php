<?php

namespace App\Models;

use App\Entities\IcomFaturamentoSolicitacao;

class IcomFaturamentoSolicitacaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'icom_faturamentos_solicitacoes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = IcomFaturamentoSolicitacao::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_cliente',
        'conta_corrente',
        'mes_referencia',
        'ano_referencia',
        'cnpj',
        'endereco',
        'telefone',
        'email',
        'contato',
        'email_secundario',
        'contato_secundario',
        'condicoes_pagamento',
        'centro_custo',
        'total_sessoes',
        'valor_total',
        'data_validacao',
        'valor_hora_contratado',
        'valor_minutos_excedidos',
        'qtde_horas_contratadas',
        'qtde_minutos_excedentes',
        'valor_faturamento_excedente',
        'assinatura_validador',
        'telefone_validador',
        'email_validador',
        'data_emissao',
        'assinatura',
        'observacoes',
        'bloqueado',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_cliente'                    => 'required|is_natural_no_zero|max_length[11]',
        'conta_corrente'                => 'string|max_length[255]',
        'mes_referencia'                => 'required|integer|max_length[2]',
        'ano_referencia'                => 'required|int|max_length[4]',
        'cnpj'                          => 'string|max_length[18]',
        'endereco'                      => 'string|max_length[255]',
        'telefone'                      => 'string|max_length[255]',
        'email'                         => 'string|max_length[255]',
        'contato'                       => 'string|max_length[255]',
        'email_secundario'              => 'string|max_length[255]',
        'contato_secundario'            => 'string|max_length[255]',
        'condicoes_pagamento'           => 'string|max_length[255]',
        'centro_custo'                  => 'string|max_length[255]',
        'total_sessoes'                 => 'required|integer|max_length[11]',
        'valor_total'                   => 'required|numeric|max_length[10]',
        'data_validacao'                => 'valid_date',
        'valor_hora_contratado'         => 'numeric|max_length[10]',
        'valor_minutos_excedidos'       => 'numeric|max_length[10]',
        'qtde_horas_contratadas'        => 'numeric|max_length[10]',
        'qtde_minutos_excedentes'       => 'numeric|max_length[10]',
        'valor_faturamento_excedente'   => 'numeric|max_length[10]',
        'assinatura_validador'          => 'string|max_length[255]',
        'telefone_validador'            => 'string|max_length[255]',
        'email_validador'               => 'string|max_length[255]',
        'data_emissao'                  => 'required|valid_date',
        'assinatura'                    => 'string|max_length[255]',
        'observacoes'                   => 'string|max_length[255]',
        'bloqueado'                     => 'integer|exact_length[1]',
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
