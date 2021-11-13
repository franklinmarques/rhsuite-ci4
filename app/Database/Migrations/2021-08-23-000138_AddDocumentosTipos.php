<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDocumentosTipos extends Migration
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
            'descricao' => [
                'type' => 'VARCHAR',
                'constraint' => '200',
            ],
            'categoria' => [
                'type' => 'INT',
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
    }

    public function down()
    {
        $this->forge->dropTable('documentos_tipos');
    }
}
