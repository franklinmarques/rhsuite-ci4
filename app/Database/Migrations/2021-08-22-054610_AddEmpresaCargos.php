<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmpresaCargos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_empresa' => [
                'type' => 'BIGINT',
                'unsigned' => true,
            ],
            'nome' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'familia_cbo' => [
                'type' => 'INT(6) ZEROFILL',
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
        $this->forge->addUniqueKey(['id_empresa', 'nome']);
        $this->forge->addForeignKey('id_empresa', 'usuarios', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('empresa_cargos');
    }

    public function down()
    {
        $this->forge->dropTable('empresa_cargos');
    }
}
