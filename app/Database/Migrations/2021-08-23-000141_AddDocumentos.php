<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDocumentos extends Migration
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
            'id_tipo' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true,
            ],
            'id_colaborador' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true,
            ],
            'descricao' => [
                'type' => 'VARCHAR',
                'constraint' => '200',
            ],
            'arquivo' => [
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
	}

	public function down()
	{
        $this->forge->dropTable('documentos');
	}
}
