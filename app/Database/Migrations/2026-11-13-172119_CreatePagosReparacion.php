<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePagosReparacion extends Migration
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
            'monto' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'metodo_pago' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'referencia' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'estado' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'PENDIENTE',
            ],
            'registrado_por' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'verificado_por' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'verificado_at' => [
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
        $this->forge->addKey('reparacion_id');
        $this->forge->addKey('estado');

        $this->forge->addForeignKey(
            'reparacion_id',
            'reparaciones',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->forge->addForeignKey(
            'registrado_por',
            'usuarios',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->forge->addForeignKey(
            'verificado_por',
            'usuarios',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->forge->createTable('pagos_reparacion', true);
    }

    public function down()
    {
        $this->forge->dropTable('pagos_reparacion', true);
    }
}
