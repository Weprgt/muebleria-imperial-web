<?php

namespace App\Models;

use CodeIgniter\Model;

class MuebleModel extends Model
{
    protected $table = 'muebles';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'categoria_id',
        'nombre',
        'descripcion',
        'precio',
        'stock',
        'stock_reservado',
        'imagen_principal',
        'activo'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
