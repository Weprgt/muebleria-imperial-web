<?php

namespace App\Models;

use CodeIgniter\Model;

class ReparacionModel extends Model
{
    protected $table = 'reparaciones';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'cliente_id',
        'canal',
        'estado',
        'diagnostico',
        'presupuesto_total',
        'anticipo_requerido',
        'decision_cliente_at',
        'fecha_inicio',
        'fecha_finalizacion',
        'fecha_entrega',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';

    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'cliente_id' => 'required|is_natural_no_zero',
        'canal'      => 'required|in_list[WEB,PRESENCIAL]',
        'estado'     => 'required|in_list[SOLICITADA,EN_EVALUACION,PRESUPUESTADA,ACEPTADA,EN_REPARACION,FINALIZADA,ENTREGADA,RECHAZADA]',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
}
