<?php

namespace App\Models;

use CodeIgniter\Model;

class PedidoModel extends Model
{
    protected $table = 'pedidos';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'cliente_id',
        'canal',
        'estado',
        'total',
        'anticipo_requerido',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';

    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'cliente_id' => 'required|is_natural_no_zero',
        'canal' => 'required|in_list[WEB,PRESENCIAL]',
        'estado' => 'required|max_length[40]',
        'total' => 'required|decimal|greater_than[0]',
        'anticipo_requerido' => 'required|decimal|greater_than_equal_to[0]',
    ];
}
