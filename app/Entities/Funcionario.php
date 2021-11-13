<?php

namespace App\Entities;

class Funcionario extends AbstractEntity
{
    protected $datamap = [];
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $casts = [
        'id_usuario' => 'int',
        'nome' => 'string',
        'data_nascimento' => '?datetime',
        'sexo' => '?string',
        'foto' => '?string',
        'assinatura_digital' => '?string',
        'nome_mae' => '?string',
        'nome_pai' => '?string',
        'endereco' => '?string',
        'numero' => '?string',
        'complemento' => '?string',
        'bairro' => '?string',
        'id_cidade' => '?int',
        'id_estado' => '?int',
        'cep' => '?string',
        'telefones' => '?string',
        'endereco_ip1' => '?string',
        'endereco_ip2' => '?string',
        'geolocalizacao_1' => '?string',
        'geolocalizacao_2' => '?string',
        'municipio_trabalho' => '?string',
        'local_trabalho' => '?string',
        'id_depto' => '?int',
        'id_area' => '?int',
        'id_setor' => '?int',
        'id_cargo' => '?int',
        'id_funcao' => '?int',
        'tipo_vinculo' => '?int',
        'rg' => '?string',
        'cpf' => '?string',
        'pis' => '?string',
        'cnpj' => '?string',
        'razao_social_cnpj' => '?string',
        'nome_fantasia_cnpj' => '?string',
        'codigo_pj' => '?string',
        'matricula' => '?string',
        'contrato' => '?string',
        'centro_custo' => '?string',
        'nome_banco' => '?string',
        'agencia_bancaria' => '?string',
        'conta_bancaria' => '?string',
        'tipo_conta_bancaria' => '?string',
        'operacao_conta_bancaria' => '?string',
        'pessoa_conta_bancaria' => '?string',
        'nome_cartao_vt' => '?string',
        'valor_vt' => '?string',
        'data_admissao' => '?string',
        'data_demissao' => '?string',
        'tipo_demissao' => '?int',
        'observacoes_demissao' => '?string',
        'nivel_acesso' => 'int',
        'hash_acesso' => '?string',
        'observacoes_historico' => '?string',
        'status' => '?int',
        'pagina_inicial' => '?string',
        'recolher_menu' => '?boolean',
        'possui_apontamento_horas' => '?boolean',
        'faz_somente_apontamento' => '?boolean',
        'banco_horas_icom' => '?string',
        'banco_horas_icom_2' => '?string',
    ];

    public function usuario(): Usuario
    {
        return $this->belongsTo(Usuario::class);
    }

    public function empresa(): Empresa
    {
        return $this->belongsTo(Empresa::class);
    }
}
