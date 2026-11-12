<?php

namespace App\Models;

use CodeIgniter\Model;

class EntregaPedidoModel extends Model
{
    protected $table            = 'entregas_pedido';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $protectFields = true;

    protected $allowedFields = [
        'pedido_id',
        'entregado_por',
        'fecha_entrega',
        'observaciones',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Fechas
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    // Validaciones
    protected $validationRules = [
        'pedido_id'     => 'required|is_natural_no_zero',
        'entregado_por' => 'required|is_natural_no_zero',
        'fecha_entrega' => 'required',
    ];

    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}