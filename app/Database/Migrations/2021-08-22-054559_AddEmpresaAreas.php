<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmpresaAreas extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_departamento' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true,
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
        $this->forge->addForeignKey('id_departamento', 'empresa_departamentos', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('empresa_areas');
    }

    public function down()
    {
        $this->forge->dropTable('empresa_areas');
    }
}
