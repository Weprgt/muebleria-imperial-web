<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDetallePedido extends Migration
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
            'mueble_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'cantidad' => [
                'type'       => 'INT',
                'unsigned'   => true,
            ],
            'precio_unitario' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'subtotal' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
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
        $this->forge->addKey('mueble_id');

        $this->forge->addUniqueKey(
            ['pedido_id', 'mueble_id']
        );

        $this->forge->addForeignKey(
            'pedido_id',
            'pedidos',
            'id',
            'CASCADE',
            'RESTRICT'
        );

        $this->forge->addForeignKey(
            'mueble_id',
            'muebles',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->forge->createTable('detalle_pedido');
    }

    public function down()
    {
        $this->forge->dropTable('detalle_pedido', true);
    }
}
