<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFuncionarios extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
            ],
            'id_empresa' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true,
            ],
            'nome' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'tipo' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
            ],
            'nivel_acesso' => [
                'type' => 'VARCHAR',
                'null' => true,
            ],
            'data_nascimento' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'sexo' => [
                'type' => 'ENUM',
                'constraint' => ['M', 'F'],
                'null' => true,
            ],
            'id_departamento' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true,
            ],
            'id_area' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true,
            ],
            'id_setor' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true,
            ],
            'id_cargo' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true,
            ],
            'id_funcao' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true,
            ],
            'municipio_trabalho' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'local_trabalho' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'foto' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'foto_descricao' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'cabecalho' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'imagem_inicial' => [
                'type' => 'TEXT',
            ],
            'tipo_tela_inicial' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'comment' => '1 - imagem padrão; 2 - vídeo padrão; 3 - imagem personalizada; 4 - vídeo personalizado',
            ],
            'pagina_inicial' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'recolher_menu' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => true,
            ],
            'imagem_fundo' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'video_fundo' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'assinatura_digital' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'tipo_vinculo' => [
                'type' => 'INT',
                'constraint' => 1,
                'null' => true,
                'comment' => '1 - CLT; 2 - MEI; 3 - PJ',
            ],
            'rg' => [
                'type' => 'VARCHAR',
                'constraint' => '13',
                'null' => true,
            ],
            'cpf' => [
                'type' => 'VARCHAR',
                'constraint' => '14',
                'null' => true,
            ],
            'cnpj' => [
                'type' => 'VARCHAR',
                'constraint' => '18',
                'null' => true,
            ],
            'pis' => [
                'type' => 'VARCHAR',
                'constraint' => '14',
                'null' => true,
            ],
            'razao_social' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'nome_fantasia' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'nome_mae' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'nome_pai' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'endereco' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'numero' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'complemento' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'bairro' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'id_estado' => [
                'type' => 'INT',
                'constraint' => 2,
                'unsigned' => true,
                'null' => true,
            ],
            'id_cidade' => [
                'type' => 'INT',
                'constraint' => 7,
                'unsigned' => true,
                'null' => true,
            ],
            'cep' => [
                'type' => 'VARCHAR',
                'constraint' => '9',
                'null' => true,
            ],
            'telefone' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'email_anterior' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'endereco_ip1' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'endereco_ip2' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'geolocalizacao_1' => [
                'type' => 'VARCHAR',
                'constraint' => '30',
                'null' => true,
            ],
            'geolocalizacao_2' => [
                'type' => 'VARCHAR',
                'constraint' => '30',
                'null' => true,
            ],
            'matricula' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'codigo_pj' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ],
            'contrato' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'centro_custo' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'nome_banco' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'agencia_bancaria' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'tipo_conta_bancaria' => [
                'type' => 'CHAR',
                'constraint' => '1',
                'null' => true,
                'comment' => 'C - Corrente; P - Poupança',
            ],
            'pessoa_conta_bancaria' => [
                'type' => 'CHAR',
                'constraint' => '1',
                'null' => true,
                'comment' => 'F- física; J - juríduca',
            ],
            'operacao_conta_bancaria' => [
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ],
            'nome_cartao' => [
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ],
            'valor_vt' => [
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ],
            'data_admissao' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'data_demissao' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'tipo_demissao' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'observacoes_demissao' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'hash_acesso' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'max_colaboradores' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'observacoes_historico' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'observacoes_avaliacao_exp' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'possui_apontamento_horas' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => true,
            ],
            'faz_somente_apontamento' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => true,
            ],
            'saldo_apontamentos' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'flag_ultimo_apontamentos' => [
                'type' => 'CHAR',
                'constraint' => '1',
                'null' => true,
                'comment' => 'E - entrada; S - saída',
            ],
            'data_hora_ultimo_apontamento' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'visulizacao_pilula_conhecimento' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => true,
            ],
            'banco_horas_icom' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ],
            'banco_horas_icom_2' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id', 'usuarios', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('id_empresa', 'empresas', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('funcionarios');
    }

    public function down()
    {
        $this->forge->dropTable('funcionarios');
    }
}
