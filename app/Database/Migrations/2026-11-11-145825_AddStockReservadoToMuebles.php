<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStockReservadoToMuebles extends Migration
{
    public function up()
    {
        $this->forge->addColumn('muebles', [
            'stock_reservado' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'default'    => 0,
                'null'       => false,
                'after'      => 'stock',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('muebles', 'stock_reservado');
    }
}
