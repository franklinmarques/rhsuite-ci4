<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEstados extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'cod_uf' => [
                'type' => 'INT',
                'constraint' => 2,
                'unsigned' => true,
            ],
            'nome' => [
                'type' => 'VARCHAR',
                'constraint' => '30',
            ],
            'uf' => [
                'type' => 'CHAR',
                'constraint' => '2',
            ],
            'cod_capital' => [
                'type' => 'INT',
                'constraint' => 7,
                'unsigned' => true,
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('cod_capital', 'cidades', 'cod_mun', 'RESTRICT', 'SET NULL');
        $this->forge->createTable('estados');
    }

    public function down()
    {
        $this->forge->dropTable('estados');
    }
}
