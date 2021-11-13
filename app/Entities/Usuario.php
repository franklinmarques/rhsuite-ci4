<?php

namespace App\Entities;

class Usuario extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_empresa' => '?int',
        'nome' => 'string',
        'tipo' => 'string',
        'url' => 'string',
        'data_nascimento' => '?date',
        'sexo' => '?string',
        'depto' => '?string',
        'area' => '?string',
        'setor' => '?string',
        'cargo' => '?string',
        'funcao' => '?string',
        'municipio' => '?string',
        'local_trabalho' => '?string',
        'id_depto' => '?int',
        'id_area' => '?int',
        'id_setor' => '?int',
        'id_cargo' => '?int',
        'id_funcao' => '?int',
        'foto' => 'string',
        'foto_descricao' => '?string',
        'cabecalho' => '?string',
        'imagem_inicial' => 'string',
        'tipo_tela_inicial' => 'bool',
        'pagina_inicial' => '?string',
        'recolher_menu' => '?bool',
        'imagem_fundo' => '?string',
        'video_fundo' => '?string',
        'assinatura_digital' => '?string',
        'tipo_vinculo' => '?int',
        'rg' => '?string',
        'cpf' => '?string',
        'cnpj' => '?string',
        'pis' => '?string',
        'razao_social' => '?string',
        'nome_fantasia' => '?string',
        'nome_mae' => '?string',
        'nome_pai' => '?string',
        'endereco' => '?string',
        'numero' => '?int',
        'complemento' => '?string',
        'bairro' => '?string',
        'id_cidade' => '?int',
        'id_estado' => '?int',
        'cep' => '?string',
        'telefone' => '?string',
        'email' => 'string',
        'senha' => 'string',
        'nova_senha' => '?string',
        'token' => 'string',
        'email_anterior' => '?string',
        'endereco_ip1' => '?string',
        'endereco_ip2' => '?string',
        'geolocalizacao_1' => '?string',
        'geolocalizacao_2' => '?string',
        'matricula' => '?string',
        'codigo_pj' => '?string',
        'contrato' => '?string',
        'centro_custo' => '?string',
        'nome_banco' => '?string',
        'agencia_bancaria' => '?string',
        'conta_bancaria' => '?string',
        'tipo_conta_bancaria' => '?string',
        'operacao_conta_bancaria' => '?string',
        'pessoa_conta_bancaria' => '?string',
        'nome_cartao' => '?string',
        'valor_vt' => '?string',
        'data_cadastro' => 'datetime',
        'data_editado' => '?datetime',
        'data_admissao' => '?datetime',
        'data_demissao' => '?date',
        'tipo_demissao' => '?int',
        'observacoes_demissao' => '?string',
        'nivel_acesso' => 'int',
        'hash_acesso' => '?string',
        'hash_acesso_original' => '?string',
        'max_colaboradores' => '?int',
        'observacoes_historico' => '?string',
        'observacoes_avaliacao_exp' => '?string',
        'status' => '?int',
        'possui_apontamento_horas' => '?bool',
        'faz_somente_apontamento' => '?bool',
        'saldo_apontamentos' => '?time',
        'flag_ultimo_apontamento' => '?string',
        'data_hora_ultimo_apontamento' => '?datetime',
        'visualizacao_pilula_conhecimento' => '?bool',
        'visualizacao_rodape' => '?bool',
        'banco_horas_icom' => '?string',
        'banco_horas_icom_2' => '?string',
    ];

    //--------------------------------------------------------------------

    public function getTipo(): Usuario
    {
        return $this;
    }

    public function getEmpresa(): ?Empresa
    {
        return $this->belongsTo(Empresa::class);
    }
}
