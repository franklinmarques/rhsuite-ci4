<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAcessoSistema extends Migration
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
            'endereco_ip' => [
                'type' => 'VARCHAR',
                'constraint' => '48',
            ],
            'agente_usuario' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'id_sessao' => [
                'type' => 'VARCHAR',
                'constraint' => '128',
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
        $this->forge->createTable('acesso_sistema');
    }

    public function down()
    {
        $this->forge->dropTable('acesso_sistema');
    }
}
