<?php

namespace App\Models;

use CodeIgniter\Model;

class DetallePedidoModel extends Model
{
    protected $table = 'detalle_pedido';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'pedido_id',
        'mueble_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';

    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'pedido_id' => 'required|is_natural_no_zero',
        'mueble_id' => 'required|is_natural_no_zero',
        'cantidad' => 'required|is_natural_no_zero',
        'precio_unitario' => 'required|decimal|greater_than[0]',
        'subtotal' => 'required|decimal|greater_than[0]',
    ];
}
