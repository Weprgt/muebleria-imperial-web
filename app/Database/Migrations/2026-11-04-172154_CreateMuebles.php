<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMuebles extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'categoria_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
            ],
            'nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'descripcion' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'precio' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'stock' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'default'    => 0,
            ],
            'imagen_principal' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'activo' => [
                'type'    => 'BOOLEAN',
                'default' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');

        $this->forge->addForeignKey(
            'categoria_id',
            'categorias_muebles',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->forge->createTable('muebles');
    }

    public function down()
    {
        $this->forge->dropTable('muebles');
    }
}
