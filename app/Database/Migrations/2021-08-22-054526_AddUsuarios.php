<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUsuarios extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nome' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'tipo' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'senha' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'token' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'status' => [
                'type' => 'SMALLINT',
                'constraint' => '2',
                'default' => 0,
            ],
            'data_ativacao' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'hash_autorizacao' => [
                'type' => 'TEXT',
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
        $this->forge->addForeignKey('id_empresa', 'usuarios', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('usuarios');
    }

    public function down()
    {
        $this->forge->dropTable('usuarios');
    }
}
