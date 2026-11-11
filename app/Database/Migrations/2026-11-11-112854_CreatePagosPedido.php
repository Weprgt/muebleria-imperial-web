<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePagosPedido extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'pedido_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'monto' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'metodo_pago' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'referencia' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'estado' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'PENDIENTE',
            ],
            'verificado_por' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
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
            'verificado_por',
            'usuarios',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->forge->createTable('pagos_pedido');
    }

    public function down()
    {
        $this->forge->dropTable('pagos_pedido', true);
    }
}
