<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReparaciones extends Migration
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
            'cliente_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'canal' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'WEB',
            ],
            'estado' => [
                'type'       => 'VARCHAR',
                'constraint' => 40,
                'default'    => 'SOLICITADA',
            ],
            'diagnostico' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'presupuesto_total' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => true,
            ],
            'anticipo_requerido' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => true,
            ],
            'decision_cliente_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'fecha_inicio' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'fecha_finalizacion' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'fecha_entrega' => [
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

        $this->forge->addKey('id', true);
        $this->forge->addKey('cliente_id');
        $this->forge->addKey('estado');

        $this->forge->addForeignKey(
            'cliente_id',
            'clientes',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->forge->createTable('reparaciones', true);
    }

    public function down()
    {
        $this->forge->dropTable('reparaciones', true);
    }
}
