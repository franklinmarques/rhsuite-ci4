<?php

namespace App\Models;

use App\Entities\RequisicaoPessoal;

class RequisicaoPessoalModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'requisicoes_pessoal';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = RequisicaoPessoal::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'numero',
        'data_abertura',
        'data_fechamento',
        'data_solicitacao_exame',
        'data_suspensao',
        'data_cancelamento',
        'data_processo_seletivo',
        'dias_ativos',
        'requisicao_confidencial',
        'tipo_vaga',
        'selecionador',
        'spa',
        'requisitante_interno',
        'requisitante_externo',
        'numero_contrato',
        'centro_custo',
        'regime_contratacao',
        'id_depto',
        'id_area',
        'id_setor',
        'id_cargo',
        'id_funcao',
        'cargo_funcao_alternativo',
        'cargo_externo',
        'funcao_externa',
        'numero_vagas',
        'vagas_deficiente',
        'justificativa_contratacao',
        'colaborador_substituto',
        'possui_indicacao',
        'colaboradores_indicados',
        'indicador_responsavel',
        'aprovado_por',
        'data_aprovacao',
        'remuneracao_mensal',
        'horario_trabalho',
        'previsao_inicio',
        'vale_transporte',
        'valor_vale_transporte',
        'vale_alimentacao',
        'valor_vale_alimentacao',
        'vale_refeicao',
        'valor_vale_refeicao',
        'assistencia_medica',
        'valor_assistencia_medica',
        'plano_odontologico',
        'valor_plano_odontologico',
        'cesta_basica',
        'valor_cesta_basica',
        'participacao_resultados',
        'valor_participacao_resultados',
        'local_trabalho',
        'municipio',
        'exame_clinico',
        'audiometria',
        'laudo_cotas',
        'exame_outros',
        'perfil_geral',
        'competencias_tecnicas',
        'competencias_comportamentais',
        'atividades_associadas',
        'observacoes',
        'observacoes_selecionador',
        'observacoes_gerais',
        'estagio',
        'status',
        'descricao_pendencias',
        'data_nascimento',
        'nome_mae',
        'nome_pai',
        'rg',
        'rg_data_emissao',
        'rg_orgao_emissor',
        'cpf',
        'pis',
        'departamento_informacoes',
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
        'numero'                        => 'required|string|max_length[255]',
        'data_abertura'                 => 'required|valid_date',
        'data_fechamento'               => 'valid_date',
        'data_solicitacao_exame'        => 'valid_date',
        'data_suspensao'                => 'valid_date',
        'data_cancelamento'             => 'valid_date',
        'data_processo_seletivo'        => 'valid_date',
        'dias_ativos'                   => 'integer|max_length[11]',
        'requisicao_confidencial'       => 'required|integer|exact_length[1]',
        'tipo_vaga'                     => 'required|string|max_length[1]',
        'selecionador'                  => 'string|max_length[255]',
        'spa'                           => 'integer|max_length[6]',
        'requisitante_interno'          => 'is_natural_no_zero|max_length[11]',
        'requisitante_externo'          => 'string|max_length[255]',
        'numero_contrato'               => 'string|max_length[255]',
        'centro_custo'                  => 'string|max_length[255]',
        'regime_contratacao'            => 'required|integer|max_length[1]',
        'id_depto'                      => 'is_natural_no_zero|max_length[11]',
        'id_area'                       => 'is_natural_no_zero|max_length[11]',
        'id_setor'                      => 'is_natural_no_zero|max_length[11]',
        'id_cargo'                      => 'is_natural_no_zero|max_length[11]',
        'id_funcao'                     => 'is_natural_no_zero|max_length[11]',
        'cargo_funcao_alternativo'      => 'string|max_length[255]',
        'cargo_externo'                 => 'string|max_length[255]',
        'funcao_externa'                => 'string|max_length[255]',
        'numero_vagas'                  => 'required|integer|max_length[11]',
        'vagas_deficiente'              => 'integer|max_length[1]',
        'justificativa_contratacao'     => 'required|string|max_length[1]',
        'colaborador_substituto'        => 'string',
        'possui_indicacao'              => 'integer|exact_length[1]',
        'colaboradores_indicados'       => 'string',
        'indicador_responsavel'         => 'string|max_length[255]',
        'aprovado_por'                  => 'string|max_length[255]',
        'data_aprovacao'                => 'valid_date',
        'remuneracao_mensal'            => 'numeric|max_length[10]',
        'horario_trabalho'              => 'string',
        'previsao_inicio'               => 'valid_date',
        'vale_transporte'               => 'integer|max_length[1]',
        'valor_vale_transporte'         => 'numeric|max_length[7]',
        'vale_alimentacao'              => 'integer|max_length[1]',
        'valor_vale_alimentacao'        => 'numeric|max_length[7]',
        'vale_refeicao'                 => 'integer|max_length[1]',
        'valor_vale_refeicao'           => 'numeric|max_length[7]',
        'assistencia_medica'            => 'integer|max_length[1]',
        'valor_assistencia_medica'      => 'numeric|max_length[7]',
        'plano_odontologico'            => 'integer|max_length[1]',
        'valor_plano_odontologico'      => 'numeric|max_length[7]',
        'cesta_basica'                  => 'integer|max_length[1]',
        'valor_cesta_basica'            => 'numeric|max_length[7]',
        'participacao_resultados'       => 'integer|max_length[1]',
        'valor_participacao_resultados' => 'numeric|max_length[7]',
        'local_trabalho'                => 'string|max_length[255]',
        'municipio'                     => 'string|max_length[30]',
        'exame_clinico'                 => 'integer|max_length[1]',
        'audiometria'                   => 'integer|max_length[1]',
        'laudo_cotas'                   => 'integer|max_length[1]',
        'exame_outros'                  => 'string|max_length[255]',
        'perfil_geral'                  => 'string',
        'competencias_tecnicas'         => 'string',
        'competencias_comportamentais'  => 'string',
        'atividades_associadas'         => 'string',
        'observacoes'                   => 'string',
        'observacoes_selecionador'      => 'string',
        'observacoes_gerais'            => 'string',
        'estagio'                       => 'required|integer|max_length[2]',
        'status'                        => 'required|string|max_length[1]',
        'descricao_pendencias'          => 'string|max_length[255]',
        'data_nascimento'               => 'valid_date',
        'nome_mae'                      => 'string|max_length[255]',
        'nome_pai'                      => 'string|max_length[255]',
        'rg'                            => 'string|max_length[14]',
        'rg_data_emissao'               => 'valid_date',
        'rg_orgao_emissor'              => 'string|max_length[100]',
        'cpf'                           => 'string|max_length[14]',
        'pis'                           => 'string|max_length[14]',
        'departamento_informacoes'      => 'string',
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

    public const TIPOS_VAGA = [
        'I' => 'Interna',
        'E' => 'Externa',
    ];
    public const REGIMES_CONTRATACAO = [
        '1' => 'CLT',
        '2' => 'MEI',
        '3' => 'PJ',
        '4' => 'Estágio',
    ];
    public const JUSTIFICATIVAS_CONTRATACAO = [
        'S' => 'Substituição',
        'T' => 'Transferência',
        'A' => 'Aumento de quadro',
        'P' => 'Temporário',
    ];
    public const TIPOS_REQUISICAO = [
        '1' => 'Confidencial',
        '0' => 'Não confidencial',
    ];
    public const ESTAGIOS = [
        '1' => '01/10 - Alinhando perfil',
        '2' => '02/10 - Divulgando vagas',
        '3' => '03/10 - Triando currículos',
        '4' => '04/10 - Convocando candidatos',
        '5' => '05/10 - Entrevistando candidatos',
        '6' => '06/10 - Elaborando pareceres',
        '7' => '07/10 - Aguardando gestor',
        '8' => '08/10 - Entrevista solicitante',
        '9' => '09/10 - Exame adissional',
        '10' => '10/10 - Entrega documentos',
        '11' => 'Faturamento',
        '12' => 'Processo finalizado',
    ];
    public const STATUS = [
        'A' => 'Ativa',
        'S' => 'Suspensa',
        'C' => 'Cancelada',
        'G' => 'Aguardando aprovação',
        'F' => 'Fechada',
        'P' => 'Fechada parcialmente',
        'R' => 'Pendências na RP',
        'D' => 'Deletada',
    ];
}
