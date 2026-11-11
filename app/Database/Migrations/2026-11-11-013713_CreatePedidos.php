<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePedidos extends Migration
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
                'type'       => 'ENUM',
                'constraint' => ['WEB', 'PRESENCIAL'],
                'default'    => 'WEB',
            ],
            'estado' => [
                'type'       => 'VARCHAR',
                'constraint' => 40,
                'default'    => 'PENDIENTE_ANTICIPO',
            ],
            'total' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'anticipo_requerido' => [
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
        $this->forge->addKey('cliente_id');
        $this->forge->addKey('estado');

        $this->forge->addForeignKey(
            'cliente_id',
            'clientes',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->forge->createTable('pedidos');
    }

    public function down()
    {
        $this->forge->dropTable('pedidos', true);
    }
}
