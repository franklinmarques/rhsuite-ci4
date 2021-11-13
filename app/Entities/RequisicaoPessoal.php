<?php

namespace App\Entities;

class RequisicaoPessoal extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_empresa' => 'int',
        'numero' => 'string',
        'data_abertura' => 'date',
        'data_fechamento' => '?date',
        'data_solicitacao_exame' => '?date',
        'data_suspensao' => '?date',
        'data_cancelamento' => '?date',
        'data_processo_seletivo' => '?date',
        'dias_ativos' => '?int',
        'requisicao_confidencial' => 'bool',
        'tipo_vaga' => 'string',
        'selecionador' => '?string',
        'spa' => '?int',
        'requisitante_interno' => '?int',
        'requisitante_externo' => '?string',
        'numero_contrato' => '?string',
        'centro_custo' => '?string',
        'regime_contratacao' => 'int',
        'id_depto' => '?int',
        'id_area' => '?int',
        'id_setor' => '?int',
        'id_cargo' => '?int',
        'id_funcao' => '?int',
        'cargo_funcao_alternativo' => '?string',
        'cargo_externo' => '?string',
        'funcao_externa' => '?string',
        'numero_vagas' => 'int',
        'vagas_deficiente' => '?int',
        'justificativa_contratacao' => 'string',
        'colaborador_substituto' => '?string',
        'possui_indicacao' => '?bool',
        'colaboradores_indicados' => '?string',
        'indicador_responsavel' => '?string',
        'aprovado_por' => '?string',
        'data_aprovacao' => '?date',
        'remuneracao_mensal' => '?decimal',
        'horario_trabalho' => '?string',
        'previsao_inicio' => '?date',
        'vale_transporte' => '?int',
        'valor_vale_transporte' => '?decimal',
        'vale_alimentacao' => '?int',
        'valor_vale_alimentacao' => '?decimal',
        'vale_refeicao' => '?int',
        'valor_vale_refeicao' => '?decimal',
        'assistencia_medica' => '?int',
        'valor_assistencia_medica' => '?decimal',
        'plano_odontologico' => '?int',
        'valor_plano_odontologico' => '?decimal',
        'cesta_basica' => '?int',
        'valor_cesta_basica' => '?decimal',
        'participacao_resultados' => '?int',
        'valor_participacao_resultados' => '?decimal',
        'local_trabalho' => '?string',
        'municipio' => '?string',
        'exame_clinico' => '?int',
        'audiometria' => '?int',
        'laudo_cotas' => '?int',
        'exame_outros' => '?string',
        'perfil_geral' => '?string',
        'competencias_tecnicas' => '?string',
        'competencias_comportamentais' => '?string',
        'atividades_associadas' => '?string',
        'observacoes' => '?string',
        'observacoes_selecionador' => '?string',
        'observacoes_gerais' => '?string',
        'estagio' => 'int',
        'status' => 'string',
        'descricao_pendencias' => '?string',
        'data_nascimento' => '?date',
        'nome_mae' => '?string',
        'nome_pai' => '?string',
        'rg' => '?string',
        'rg_data_emissao' => '?date',
        'rg_orgao_emissor' => '?string',
        'cpf' => '?string',
        'pis' => '?string',
        'departamento_informacoes' => '?string',
    ];
}