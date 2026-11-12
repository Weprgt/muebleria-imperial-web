<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEntregasPedido extends Migration
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
            'entregado_por' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'fecha_entrega' => [
                'type' => 'DATETIME',
            ],
            'observaciones' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');

        // Impide registrar dos entregas para un mismo pedido.
        $this->forge->addUniqueKey('pedido_id');

        $this->forge->addKey('entregado_por');

        $this->forge->addForeignKey(
            'pedido_id',
            'pedidos',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->forge->addForeignKey(
            'entregado_por',
            'usuarios',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->forge->createTable('entregas_pedido');
    }

    public function down()
    {
        $this->forge->dropTable('entregas_pedido', true);
    }
}
