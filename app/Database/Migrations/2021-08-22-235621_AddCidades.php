<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCidades extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'cod_mun' => [
                'type' => 'INT',
                'constraint' => 7,
                'unsigned' => true,
            ],
            'cod_uf' => [
                'type' => 'INT',
                'constraint' => 2,
                'unsigned' => true,
            ],
            'nome' => [
                'type' => 'VARCHAR',
                'constraint' => '40',
            ],
        ]);
        $this->forge->addKey('cod_mun', true);
        $this->forge->addForeignKey('cod_uf', 'estados', 'cod_uf', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('cidades');
    }

    public function down()
    {
        $this->forge->dropTable('cidades');
    }
}
