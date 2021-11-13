<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmpresas extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
            ],
            'nome' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'url' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'url_departamento' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => 'Departamento',
            ],
            'url_departamentos' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => 'Departamentos',
            ],
            'url_area' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => 'Área',
            ],
            'url_areas' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => 'Áreas',
            ],
            'url_setor' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => 'Setor',
            ],
            'url_setores' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => 'Setores',
            ],
            'url_cargo' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => 'Cargo',
            ],
            'url_cargos' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => 'Cargos',
            ],
            'url_funcao' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => 'Função',
            ],
            'url_funcoes' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => 'Funções',
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
        $this->forge->addForeignKey('id', 'usuarios', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('empresas');
    }

    public function down()
    {
        $this->forge->dropTable('empresas');
    }
}
