<?php

namespace App\Models;

use CodeIgniter\Model;

class HistorialEstadoReparacionModel extends Model
{
    protected $table = 'historial_estados_reparacion';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'reparacion_id',
        'estado_anterior',
        'estado_nuevo',
        'observaciones',
        'usuario_id',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';

    protected $createdField = 'created_at';
    protected $updatedField = '';

    protected $validationRules = [
        'reparacion_id' => 'required|is_natural_no_zero',
        'estado_anterior' => 'permit_empty|max_length[40]',
        'estado_nuevo' => 'required|in_list[SOLICITADA,EN_EVALUACION,PRESUPUESTADA,ACEPTADA,EN_REPARACION,FINALIZADA,ENTREGADA,RECHAZADA]',
        'observaciones' => 'permit_empty',
        'usuario_id' => 'required|is_natural_no_zero',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
}
