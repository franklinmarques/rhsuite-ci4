<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmpresaDepartamentos extends Migration
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
        $this->forge->addForeignKey('id_empresa', 'usuarios', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('empresa_departamentos');
    }

    public function down()
    {
        $this->forge->dropTable('empresa_departamentos');
    }
}
