<?php

namespace App\Models;

use CodeIgniter\Model;

class PagoReparacionModel extends Model
{
    protected $table = 'pagos_reparacion';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'reparacion_id',
        'monto',
        'metodo_pago',
        'referencia',
        'estado',
        'registrado_por',
        'verificado_por',
        'verificado_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';

    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'reparacion_id' => 'required|is_natural_no_zero',
        'monto' => 'required|decimal|greater_than[0]',
        'metodo_pago' => 'required|in_list[EFECTIVO,TRANSFERENCIA,DEPOSITO]',
        'referencia' => 'permit_empty|max_length[150]',
        'estado' => 'required|in_list[PENDIENTE,VERIFICADO,RECHAZADO]',
        'registrado_por' => 'permit_empty|is_natural_no_zero',
        'verificado_por' => 'permit_empty|is_natural_no_zero',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
}
