<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReembolsosPedido extends Migration
{
    public function up()
    {
        // La tabla ya existe en esta base de datos.
        if ($this->db->tableExists('reembolsos_pedido')) {
            return;
        }

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
            'monto' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'metodo_reembolso' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'referencia' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'motivo' => [
                'type' => 'TEXT',
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
            ],
            'procesado_por' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'procesado_at' => [
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
            'registrado_por',
            'usuarios',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->forge->addForeignKey(
            'procesado_por',
            'usuarios',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->forge->createTable('reembolsos_pedido', true);
    }

    public function down()
    {
        $this->forge->dropTable('reembolsos_pedido', true);
    }
}
