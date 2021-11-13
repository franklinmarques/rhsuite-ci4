<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEscolaridades extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 2,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nome' => [
                'type' => 'VARCHAR',
                'unsigned' => '100',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('escolaridade');
    }

    public function down()
    {
        $this->forge->dropTable('escolaridade');
    }
}
