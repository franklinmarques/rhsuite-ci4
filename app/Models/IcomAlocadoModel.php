<?php

namespace App\Models;

use App\Entities\IcomAlocado;

class IcomAlocadoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'icom_alocados';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = IcomAlocado::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_alocacao',
        'id_usuario',
        'nome_usuario',
        'id_funcao',
        'matricula',
        'categoria',
        'nota_fiscal_pagto',
        'data_emissao_pagto',
        'codigo_alfa_pagto',
        'status_pagto',
        'justificativa_pagto',
        'data_liberacao_pagto',
        'data_emissao_nota',
        'data_aprovacao_pagto',
        'id_usuario_aprovador_pagto',
        'nome_aprovador_pagto',
        'observacoes_pagto',
        'qtde_horas_pagto',
        'valor_total_pagto',
        'valor_hora_mei',
        'qtde_horas_mei',
        'qtde_horas_dia_mei',
        'valor_mes_clt',
        'qtde_meses_clt',
        'qtde_horas_dia_clt',
        'horario_entrada',
        'horario_intervalo',
        'horario_retorno',
        'horario_saida',
        'desconto_folha',
        'comprometimento',
        'pontualidade',
        'script',
        'simpatia',
        'empatia',
        'postura',
        'ferramenta',
        'tradutorio',
        'linguistico',
        'neutralidade',
        'discricao',
        'fidelidade',
        'tempo_medio',
        'qtde_atendidas',
        'qtde_recusadas',
        'taxa_ocupacao',
        'taxa_absenteismo',
        'qtde_reclamacoes',
        'extra_1',
        'extra_2',
        'extra_3',
        'feedback',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_alocacao'                   => 'required|is_natural_no_zero|max_length[11]',
        'id_usuario'                    => 'is_natural_no_zero|max_length[11]',
        'nome_usuario'                  => 'required|string|max_length[255]',
        'id_funcao'                     => 'integer|max_length[11]',
        'matricula'                     => 'integer|max_length[11]',
        'categoria'                     => 'required|string|max_length[3]',
        'nota_fiscal_pagto'             => 'string|max_length[255]',
        'data_emissao_pagto'            => 'valid_date',
        'codigo_alfa_pagto'             => 'string|max_length[100]',
        'status_pagto'                  => 'integer|exact_length[1]',
        'justificativa_pagto'           => 'string|max_length[255]',
        'data_liberacao_pagto'          => 'valid_date',
        'data_emissao_nota'             => 'valid_date',
        'data_aprovacao_pagto'          => 'valid_date',
        'id_usuario_aprovador_pagto'    => 'is_natural_no_zero|max_length[11]',
        'nome_aprovador_pagto'          => 'string|max_length[255]',
        'observacoes_pagto'             => 'string',
        'qtde_horas_pagto'              => 'valid_time',
        'valor_total_pagto'             => 'numeric|max_length[10]',
        'valor_hora_mei'                => 'numeric|max_length[10]',
        'qtde_horas_mei'                => 'valid_time',
        'qtde_horas_dia_mei'            => 'valid_time',
        'valor_mes_clt'                 => 'numeric|max_length[10]',
        'qtde_meses_clt'                => 'valid_time',
        'qtde_horas_dia_clt'            => 'valid_time',
        'horario_entrada'               => 'valid_time',
        'horario_intervalo'             => 'valid_time',
        'horario_retorno'               => 'valid_time',
        'horario_saida'                 => 'valid_time',
        'desconto_folha'                => 'string|max_length[10]',
        'comprometimento'               => 'integer|exact_length[1]',
        'pontualidade'                  => 'integer|exact_length[1]',
        'script'                        => 'integer|exact_length[1]',
        'simpatia'                      => 'integer|exact_length[1]',
        'empatia'                       => 'integer|exact_length[1]',
        'postura'                       => 'integer|exact_length[1]',
        'ferramenta'                    => 'integer|exact_length[1]',
        'tradutorio'                    => 'integer|exact_length[1]',
        'linguistico'                   => 'integer|exact_length[1]',
        'neutralidade'                  => 'integer|exact_length[1]',
        'discricao'                     => 'integer|exact_length[1]',
        'fidelidade'                    => 'integer|exact_length[1]',
        'tempo_medio'                   => 'valid_time',
        'qtde_atendidas'                => 'integer|max_length[11]',
        'qtde_recusadas'                => 'integer|max_length[11]',
        'taxa_ocupacao'                 => 'numeric|max_length[5]',
        'taxa_absenteismo'              => 'numeric|max_length[5]',
        'qtde_reclamacoes'              => 'integer|max_length[11]',
        'extra_1'                       => 'integer|exact_length[1]',
        'extra_2'                       => 'integer|exact_length[1]',
        'extra_3'                       => 'integer|exact_length[1]',
        'feedback'                      => 'string',
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
	protected $beforeDelete         = ['restaurarSaldoBancoHoras'];
	protected $afterDelete          = ['atualizarSaldoBancoHoras'];

    //--------------------------------------------------------------------

    public const CATEGORIAS = [
        'CLT' => 'CLT',
        'MEI' => 'MEI',
    ];
    public const STATUS_PAGTO = [
        '' => 'Validar valor',
        '1' => 'Eu concordo com o valor a ser pago',
    ];
    public const NIVEIS_PERFORMANCE = [
        '1' => 'Sem qualidade, muito abaixo das expectativas',
        '2' => 'Sem qualidade, abaixo das expectativas',
        '3' => 'Sem qualidade, porém alcança as expectativas e/ou com qualidade, porém não alcança as expectativas',
        '4' => 'Com qualidade, porém há necessidade de melhorias para alcançar as expectativas',
        '5' => 'Com qualidade, alcança as expectativas',
    ];
    public const NIVEIS_PERFORMANCE_OLD = [
        '1' => 'Muito abaixo do necessário',
        '2' => 'Um pouco abaixo do necessário',
        '3' => 'Dentro da média e do necessário',
        '4' => 'Um pouco acima do necessário',
        '5' => 'Muito acima do necessário',
    ];

    //--------------------------------------------------------------------

    protected function restaurarSaldoBancoHoras($data)
    {
        $this->db->trans_start();

        if (!empty($data[$this->primaryKey]) == false) {
            return $data;
        }

        $row = $this->db
            ->select('a.id_usuario, b.banco_horas_icom')
            ->select("SUM(TIME_TO_SEC(c.saldo_banco_horas)) AS saldo_banco_horas", false)
            ->join('usuarios b', 'b.id = a.id_usuario')
            ->join('icom_apontamentos c', 'c.id_alocado = a.id', 'left')
            ->where_in('a.id', $data[$this->primaryKey])
            ->where('c.saldo_banco_horas IS NOT NULL')
            ->get('icom_alocados a')
            ->row();

        if (empty($row)) {
            return $data;
        }

        $this->load->helper('time');

        $bancoHoras = timeToSec($row->banco_horas_icom) - ($row->saldo_banco_horas ?? 0);

        $this->db->update('usuarios', ['banco_horas_icom' => secToTime($bancoHoras)], ['id' => $row->id_usuario]);

        return $data;
    }

    //--------------------------------------------------------------------

    protected function atualizarSaldoBancoHoras($data)
    {
        $this->db->trans_complete();

        return $data;
    }
}
