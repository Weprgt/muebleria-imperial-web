<?php

namespace App\Models;

use CodeIgniter\Model;

class DetalleReparacionModel extends Model
{
    protected $table = 'detalle_reparacion';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'reparacion_id',
        'nombre_mueble',
        'descripcion_problema',
        'fotografia',
        'cantidad',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';

    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'reparacion_id' => 'required|is_natural_no_zero',
        'nombre_mueble' => 'required|max_length[150]',
        'descripcion_problema' => 'required',
        'cantidad' => 'required|is_natural_no_zero',
        'fotografia' => 'permit_empty|max_length[255]',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
}
