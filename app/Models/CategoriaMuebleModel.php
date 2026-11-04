<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoriaMuebleModel extends Model
{
    protected $table = 'categorias_muebles';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = [
        'nombre',
        'descripcion',
        'activo'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
