<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmpresaSetores extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_area' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true,
            ],
            'nome' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'cnpj' => [
                'type' => 'VARCHAR',
                'constraint' => '18',
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
        $this->forge->addForeignKey('id_area', 'empresa_areas', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('empresa_setores');
    }

    public function down()
    {
        $this->forge->dropTable('empresa_setores');
    }
}
