<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCategoriasMuebles extends Migration
{
    public function up() // define cómo crear la tabla
    {
        $this->forge->addField([ // define sus columnas
            'id' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
                'auto_increment'    => true,
            ],
            'nombre' => [
                'type'              => 'VARCHAR',
                'constraint'        => 100,
            ],
            'descripcion' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'activo' => [
                'type'      => 'BOOLEAN',
                'default'   => true,
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

        $this->forge->addKey('id', true); // establece la llave primaria
        $this->forge->addUniqueKey('nombre'); // evita categorías con nombres exactamente iguales según la copmaración de MySQL
        $this->forge->createTable('categorias_muebles'); // crea la tabla cuando se ejecuta la migración
    }

    public function down() // permite revertir la migración si fuera necesaria
    {
        $this->forge->dropTable('categorias_muebles');
    }
}
