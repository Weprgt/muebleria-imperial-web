<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSolicitudesCancelacion extends Migration
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
            'pedido_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'solicitado_por' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'motivo' => [
                'type'       => 'TEXT',
            ],
            'estado' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'PENDIENTE',
            ],
            'resuelto_por' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'observacion_resolucion' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'resuelto_at' => [
                'type' => 'DATETIME',
                'null' => true,
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

        $this->forge->addKey('pedido_id');
        $this->forge->addKey('estado');

        $this->forge->addForeignKey(
            'pedido_id',
            'pedidos',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->forge->addForeignKey(
            'solicitado_por',
            'usuarios',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->forge->addForeignKey(
            'resuelto_por',
            'usuarios',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->forge->createTable('solicitudes_cancelacion');
    }

    public function down()
    {
        $this->forge->dropTable('solicitudes_cancelacion', true);
    }
}
