<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDetalleReparacion extends Migration
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
            'reparacion_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'nombre_mueble' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'descripcion_problema' => [
                'type' => 'TEXT',
            ],
            'fotografia' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'cantidad' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 1,
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

        $this->forge->addKey('id', true);
        $this->forge->addKey('reparacion_id');

        $this->forge->addForeignKey(
            'reparacion_id',
            'reparaciones',
            'id',
            'CASCADE',
            'RESTRICT'
        );

        $this->forge->createTable('detalle_reparacion', true);
    }

    public function down()
    {
        $this->forge->dropTable('detalle_reparacion', true);
    }
}
