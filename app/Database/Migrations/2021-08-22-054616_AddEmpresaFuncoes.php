<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmpresaFuncoes extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_cargo' => [
                'type' => 'BIGINT',
                'unsigned' => true,
            ],
            'nome' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'ocupacao_cbo' => [
                'type' => 'INT(2) ZEROFILL',
                'unsigned' => true,
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
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['id_cargo', 'nome']);
        $this->forge->addForeignKey('id_cargo', 'empresa_cargos', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('empresa_funcoes');
    }

    public function down()
    {
        $this->forge->dropTable('empresa_funcoes');
    }
}
