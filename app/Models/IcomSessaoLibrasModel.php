<?php

namespace App\Models;

use App\Entities\IcomSessaoLibras;

class IcomSessaoLibrasModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'icom_sessoes_libras';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = IcomSessaoLibras::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_produto',
        'id_cliente',
        'codigo_contrato',
        'data_evento',
        'id_recursao',
        'tipo_recursao',
        'data_inicio_recursao',
        'data_termino_recursao',
        'semanas_recursao',
        'qtde_recursoes',
        'horario_inicio',
        'horario_termino',
        'qtde_horas',
        'titulo_evento',
        'local_evento',
        'requisitante_evento',
        'valor_faturamento',
        'valor_desconto',
        'custo_operacional',
        'custo_impostos',
        'nota_fiscal_faturamento',
        'id_depto_prestador_servico',
        'id_profissional_alocado',
        'valor_faturamento_profissional',
        'valor_pagamento_profissional',
        'nota_fiscal_pagamento',
        'status',
        'data_criacao',
        'data_cancelamento',
        'horario_cancelamento',
        'observacoes',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_produto'                        => 'required|is_natural_no_zero|max_length[11]',
        'id_cliente'                        => 'required|is_natural_no_zero|max_length[11]',
        'codigo_contrato'                   => 'is_natural_no_zero|max_length[11]',
        'data_evento'                       => 'required|valid_date',
        'id_recursao'                       => 'integer|max_length[11]',
        'tipo_recursao'                     => 'integer|exact_length[1]',
        'data_inicio_recursao'              => 'valid_date',
        'data_termino_recursao'             => 'valid_date',
        'semanas_recursao'                  => 'string|max_length[13]',
        'qtde_recursoes'                    => 'integer|max_length[1]',
        'horario_inicio'                    => 'required|valid_time',
        'horario_termino'                   => 'required|valid_time',
        'qtde_horas'                        => 'required|numeric|max_length[6]',
        'titulo_evento'                     => 'string|max_length[255]',
        'local_evento'                      => 'string',
        'requisitante_evento'               => 'string|max_length[255]',
        'valor_faturamento'                 => 'numeric|max_length[10]',
        'valor_desconto'                    => 'numeric|max_length[10]',
        'custo_operacional'                 => 'numeric|max_length[10]',
        'custo_impostos'                    => 'numeric|max_length[10]',
        'nota_fiscal_faturamento'           => 'string|max_length[100]',
        'id_depto_prestador_servico'        => 'required|integer|max_length[11]',
        'id_profissional_alocado'           => 'integer|max_length[11]',
        'valor_faturamento_profissional'    => 'numeric|max_length[10]',
        'valor_pagamento_profissional'      => 'numeric|max_length[10]',
        'nota_fiscal_pagamento'             => 'string|max_length[100]',
        'status'                            => 'required|integer|exact_length[1]',
        'data_criacao'                      => 'valid_date',
        'data_cancelamento'                 => 'valid_date',
        'horario_cancelamento'              => 'valid_time',
        'observacoes'                       => 'string',
    ];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = ['setDataCriacao'];
	protected $afterInsert          = [];
	protected $beforeUpdate         = [];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = ['excluirSolicitacaoFaturamento', 'excluirSolicitacaoPagamento'];
	protected $afterDelete          = [];

    //--------------------------------------------------------------------

    public const TIPOS_RECURSAO = [
        '1' => 'Dias',
        '2' => 'Semanas',
        '3' => 'Meses',
    ];
    public const STATUS = [
        '1' => 'Ativo/confirmado',
        '2' => 'Cancelado (cobrar)',
        '3' => 'Cancelado (não cobrar)',
        '4' => 'Realizado',
    ];

    //--------------------------------------------------------------------

    protected function setDataCriacao($data)
    {
        if (array_key_exists('data', $data) === false) {
            return $data;
        }

        $data['data']['data_criacao'] = date('Y-m-d H:i:s');

        return $data;
    }

    //--------------------------------------------------------------------

    protected function excluirSolicitacaoFaturamento($data)
    {
        if (!empty($data['id']) == false) {
            return $data;
        }

        $sessoesLibras = $this->find($data['id']);

        if (!is_array($sessoesLibras)) {
            $sessoesLibras = [$sessoesLibras];
        }

        foreach ($sessoesLibras as $sessaoLibras) {
            $this->db
                ->where('id_cliente', $sessaoLibras->id_cliente)
                ->where('mes_referencia', date('m', strtotime($sessaoLibras->data_evento)))
                ->where('ano_referencia', date('m', strtotime($sessaoLibras->data_evento)))
                ->delete('icom_faturamentos_solicitacoes');
        }

        return $data;
    }

    //--------------------------------------------------------------------

    protected function excluirSolicitacaoPagamento($data)
    {
        if (!empty($data['id']) == false) {
            return $data;
        }

        $sessoesLibras = $this->find($data['id']);

        if (!is_array($sessoesLibras)) {
            $sessoesLibras = [$sessoesLibras];
        }

        foreach ($sessoesLibras as $sessaoLibras) {
            $this->db
                ->where('id_profissional_alocado', $sessaoLibras->id_profissional_alocado)
                ->where('mes_referencia', date('m', strtotime($sessaoLibras->data_evento)))
                ->where('ano_referencia', date('m', strtotime($sessaoLibras->data_evento)))
                ->delete('icom_pagamentos_solicitacoes');
        }

        return $data;
    }
}
