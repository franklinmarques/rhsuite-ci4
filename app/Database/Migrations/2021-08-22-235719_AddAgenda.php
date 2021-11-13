<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAgenda extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_usuario' => [
                'type' => 'BIGINT',
                'unsigned' => true,
            ],
            'id_usuario_referenciado' => [
                'type' => 'BIGINT',
                'unsigned' => true,
            ],
            'tipo' => [
                'type' => 'INT',
                'constraint' => 3,
            ],
            'titulo' => [
                'type' => 'VARCHAR',
                'constraint' => '165',
            ],
            'descricao' => [
                'type' => 'LONGTEXT',
            ],
            'link' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'cor' => [
                'type' => 'VARCHAR',
                'constraint' => '7',
                'null' => true,
            ],
            'status' => [
                'type' => 'SMALLINT',
                'constraint' => 1,
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
        $this->forge->addForeignKey('id_usuario', 'usuarios', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('agenda');
    }

    public function down()
    {
        $this->forge->dropTable('agenda');
    }
}
