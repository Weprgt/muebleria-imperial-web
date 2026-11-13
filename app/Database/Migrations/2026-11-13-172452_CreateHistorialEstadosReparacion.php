<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHistorialEstadosReparacion extends Migration
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
            'estado_anterior' => [
                'type'       => 'VARCHAR',
                'constraint' => 40,
                'null'       => true,
            ],
            'estado_nuevo' => [
                'type'       => 'VARCHAR',
                'constraint' => 40,
            ],
            'observaciones' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'usuario_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('reparacion_id');
        $this->forge->addKey('usuario_id');

        $this->forge->addForeignKey(
            'reparacion_id',
            'reparaciones',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->forge->addForeignKey(
            'usuario_id',
            'usuarios',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->forge->createTable(
            'historial_estados_reparacion',
            true
        );
    }

    public function down()
    {
        $this->forge->dropTable(
            'historial_estados_reparacion',
            true
        );
    }
}
