<?php

namespace App\Controllers;

use App\Models\ReparacionModel;

class GestionReparacionesController extends BaseController
{
    public function index()
    {
        $rolId = (int) session('rol_id');

        if (
            session('logueado') !== true ||
            !in_array($rolId, [1, 3], true)
        ) {
            return redirect()->to(site_url('dashboard'))
                ->with('error', 'No tienes permiso para gestionar reparaciones.');
        }

        $reparaciones = (new ReparacionModel())
            ->select('reparaciones.*, usuarios.nombre AS nombre_cliente, usuarios.apellido AS apellido_cliente')
            ->select("(SELECT GROUP_CONCAT(nombre_mueble SEPARATOR ', ')
                    FROM detalle_reparacion
                    WHERE detalle_reparacion.reparacion_id = reparaciones.id) AS muebles", false)
            ->join('clientes', 'clientes.id = reparaciones.cliente_id')
            ->join('usuarios', 'usuarios.id = clientes.usuario_id')
            ->orderBy('reparaciones.id', 'DESC')
            ->findAll();

        return view('taller/reparaciones/index', [
            'reparaciones' => $reparaciones,
        ]);
    }

    public function iniciarEvaluacion(int $id)
    {
        $rolId = (int) session('rol_id');

        if (
            session('logueado') !== true ||
            !in_array($rolId, [1, 3], true)
        ) {
            return redirect()->to(site_url('dashboard'))
                ->with('error', 'No tienes permiso para gestionar reparaciones.');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $reparacion = $db->table('reparaciones')
                ->where('id', $id)
                ->get()
                ->getRowArray();

            if (!$reparacion || $reparacion['estado'] !== 'SOLICITADA') {
                throw new \RuntimeException(
                    'La reparación no existe o no está pendiente de evaluación.'
                );
            }

            $actualizadas = $db->table('reparaciones')
                ->where('id', $id)
                ->where('estado', 'SOLICITADA')
                ->update([
                    'estado' => 'EN_EVALUACION',
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            if (!$actualizadas || $db->affectedRows() !== 1) {
                throw new \RuntimeException('No se pudo iniciar la evaluación.');
            }

            $historialId = (new \App\Models\HistorialEstadoReparacionModel())
                ->insert([
                    'reparacion_id' => $id,
                    'estado_anterior' => 'SOLICITADA',
                    'estado_nuevo' => 'EN_EVALUACION',
                    'observaciones' => 'Evaluación iniciada por el personal autorizado.',
                    'usuario_id' => session('usuario_id'),
                ], true);

            if (!$historialId || $db->transStatus() === false) {
                throw new \RuntimeException('No se pudo registrar el historial.');
            }

            if ($db->transCommit() === false) {
                throw new \RuntimeException('No se pudo confirmar la operación.');
            }

            return redirect()->to(site_url('taller/reparaciones'))
                ->with('success', 'La evaluación se inició correctamente.');

        } catch (\Throwable $e) {
            $db->transRollback();

            log_message('error', 'Error al iniciar evaluación: ' . $e->getMessage());

            return redirect()->to(site_url('taller/reparaciones'))
                ->with('error', 'No fue posible iniciar la evaluación.');
        }
    }

    public function presupuestar(int $id)
    {
        $rolId = (int) session('rol_id');

        if (
            session('logueado') !== true ||
            !in_array($rolId, [1, 3], true)
        ) {
            return redirect()->to(site_url('dashboard'))
                ->with('error', 'No tienes permiso para gestionar reparaciones.');
        }

        $reparacion = (new ReparacionModel())
            ->select('reparaciones.*, usuarios.nombre AS nombre_cliente, usuarios.apellido AS apellido_cliente')
            ->join('clientes', 'clientes.id = reparaciones.cliente_id')
            ->join('usuarios', 'usuarios.id = clientes.usuario_id')
            ->where('reparaciones.id', $id)
            ->first();

        if (!$reparacion) {
            return redirect()->to(site_url('taller/reparaciones'))
                ->with('error', 'La reparación solicitada no existe.');
        }

        if ($reparacion['estado'] !== 'EN_EVALUACION') {
            return redirect()->to(site_url('taller/reparaciones'))
                ->with('error', 'Esta reparación no está en evaluación.');
        }

        $detalles = (new \App\Models\DetalleReparacionModel())
            ->where('reparacion_id', $id)
            ->findAll();

        return view('taller/reparaciones/presupuestar', [
            'reparacion' => $reparacion,
            'detalles' => $detalles,
        ]);
    }

    public function guardarPresupuesto(int $id)
    {
        $rolId = (int) session('rol_id');

        if (
            session('logueado') !== true ||
            !in_array($rolId, [1, 3], true)
        ) {
            return redirect()->to(site_url('dashboard'))
                ->with('error', 'No tienes permiso para gestionar reparaciones.');
        }

        $datos = $this->request->getPost([
            'diagnostico',
            'presupuesto_total',
        ]);

        $reglas = [
            'diagnostico' => 'required|min_length[10]|max_length[2000]',
            'presupuesto_total' => 'required|decimal|greater_than[0]',
        ];

        if (!$this->validateData($datos, $reglas)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $diagnostico = trim($datos['diagnostico']);
        $presupuesto = round((float) $datos['presupuesto_total'], 2);
        $anticipo = round($presupuesto * 0.50, 2);

        if ($presupuesto <= 0 || $anticipo <= 0) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'El presupuesto debe ser mayor que cero.');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $reparacion = $db->table('reparaciones')
                ->where('id', $id)
                ->get()
                ->getRowArray();

            if (!$reparacion || $reparacion['estado'] !== 'EN_EVALUACION') {
                throw new \RuntimeException(
                    'La reparación no existe o no está en evaluación.'
                );
            }

            $actualizada = $db->table('reparaciones')
                ->where('id', $id)
                ->where('estado', 'EN_EVALUACION')
                ->update([
                    'diagnostico' => $diagnostico,
                    'presupuesto_total' => $presupuesto,
                    'anticipo_requerido' => $anticipo,
                    'estado' => 'PRESUPUESTADA',
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            if (!$actualizada || $db->affectedRows() !== 1) {
                throw new \RuntimeException(
                    'No se pudo registrar el presupuesto.'
                );
            }

            $historialId = (new \App\Models\HistorialEstadoReparacionModel())
                ->insert([
                    'reparacion_id' => $id,
                    'estado_anterior' => 'EN_EVALUACION',
                    'estado_nuevo' => 'PRESUPUESTADA',
                    'observaciones' => 'Diagnóstico y presupuesto registrados.',
                    'usuario_id' => session('usuario_id'),
                ], true);

            if (!$historialId || $db->transStatus() === false) {
                throw new \RuntimeException(
                    'No se pudo registrar el historial.'
                );
            }

            if ($db->transCommit() === false) {
                throw new \RuntimeException(
                    'No se pudo confirmar la operación.'
                );
            }

            return redirect()
                ->to(site_url('taller/reparaciones'))
                ->with('success', 'Diagnóstico y presupuesto registrados correctamente.');

        } catch (\Throwable $e) {
            $db->transRollback();

            log_message(
                'error',
                'Error al guardar presupuesto de reparación: ' . $e->getMessage()
            );

            return redirect()
                ->to(site_url('taller/reparaciones'))
                ->with('error', 'No fue posible registrar el presupuesto.');
        }
    }

    public function iniciarReparacion(int $id)
    {
        $rolId = (int) session('rol_id');
        $usuarioId = (int) session('usuario_id');

        if (
            session('logueado') !== true ||
            !in_array($rolId, [1, 3], true) ||
            $usuarioId <= 0
        ) {
            return redirect()->to(site_url('dashboard'))
                ->with('error', 'No tienes permiso para iniciar reparaciones.');
        }

        $db = db_connect();

        try {
            $db->transBegin();

            // Bloquear la reparación para evitar cambios simultáneos.
            $reparacion = $db->query(
                'SELECT id, estado, anticipo_requerido
                FROM reparaciones
                WHERE id = ?
                FOR UPDATE',
                [$id]
            )->getRowArray();

            if (
                $reparacion === null ||
                $reparacion['estado'] !== 'ACEPTADA'
            ) {
                throw new \RuntimeException(
                    'La reparación no existe o no está aceptada.'
                );
            }

            // Convertir importes a centavos.
            $aCentavos = static function (string $importe): int {
                if (!preg_match('/^\d+(\.\d{1,2})?$/', $importe)) {
                    throw new \RuntimeException('Importe inválido.');
                }

                [$entero, $decimal] = array_pad(
                    explode('.', $importe, 2),
                    2,
                    '0'
                );

                return ((int) $entero * 100)
                    + (int) str_pad($decimal, 2, '0');
            };

            $anticipo = $aCentavos(
                (string) $reparacion['anticipo_requerido']
            );

            if ($anticipo <= 0) {
                throw new \RuntimeException(
                    'La reparación no tiene un anticipo válido.'
                );
            }

            // Consultar únicamente pagos verificados.
            $pagos = $db->table('pagos_reparacion')
                ->select('monto')
                ->where('reparacion_id', $id)
                ->where('estado', 'VERIFICADO')
                ->get()
                ->getResultArray();

            $totalVerificado = 0;

            foreach ($pagos as $pago) {
                $totalVerificado += $aCentavos(
                    (string) $pago['monto']
                );
            }

            if ($totalVerificado < $anticipo) {
                throw new \RuntimeException(
                    'No se puede iniciar sin el anticipo verificado.'
                );
            }

            $ahora = date('Y-m-d H:i:s');

            // Cambiar el estado de la reparación.
            $db->table('reparaciones')
                ->where('id', $id)
                ->where('estado', 'ACEPTADA')
                ->update([
                    'estado' => 'EN_REPARACION',
                    'fecha_inicio' => $ahora,
                    'updated_at' => $ahora,
                ]);

            if (
                $db->transStatus() === false ||
                $db->affectedRows() !== 1
            ) {
                throw new \RuntimeException(
                    'No se pudo iniciar la reparación.'
                );
            }

            // Registrar el cambio en el historial.
            $historialId = (new \App\Models\HistorialEstadoReparacionModel())
                ->insert([
                    'reparacion_id' => $id,
                    'estado_anterior' => 'ACEPTADA',
                    'estado_nuevo' => 'EN_REPARACION',
                    'observaciones' =>
                        'Reparación iniciada con anticipo verificado.',
                    'usuario_id' => $usuarioId,
                ], true);

            if (!$historialId || $db->transStatus() === false) {
                throw new \RuntimeException(
                    'No se pudo registrar el historial.'
                );
            }

            if (
                $db->transStatus() === false ||
                !$db->transCommit()
            ) {
                throw new \RuntimeException(
                    'No se pudo completar la transacción.'
                );
            }

            return redirect()->to(site_url('taller/reparaciones'))
                ->with('success', 'La reparación se inició correctamente.');

        } catch (\Throwable $e) {
            $db->transRollback();

            log_message(
                'error',
                'Error al iniciar reparación #' . $id . ': '
                . $e->getMessage()
            );

            return redirect()->to(site_url('taller/reparaciones'))
                ->with('error', 'No fue posible iniciar la reparación.');
        }
    }

    public function finalizarReparacion(int $id)
    {
        $rolId = (int) session('rol_id');
        $usuarioId = (int) session('usuario_id');

        if (
            session('logueado') !== true ||
            !in_array($rolId, [1, 3], true) ||
            $usuarioId <= 0
        ) {
            return redirect()->to(site_url('dashboard'))
                ->with('error', 'No tienes permiso para finalizar reparaciones.');
        }

        $db = db_connect();

        try {
            $db->transBegin();

            $reparacion = $db->query(
                'SELECT id, estado
                FROM reparaciones
                WHERE id = ?
                FOR UPDATE',
                [$id]
            )->getRowArray();

            if (
                $reparacion === null ||
                $reparacion['estado'] !== 'EN_REPARACION'
            ) {
                throw new \RuntimeException(
                    'La reparación no existe o no está en proceso.'
                );
            }

            $ahora = date('Y-m-d H:i:s');

            $db->table('reparaciones')
                ->where('id', $id)
                ->where('estado', 'EN_REPARACION')
                ->update([
                    'estado' => 'FINALIZADA',
                    'fecha_finalizacion' => $ahora,
                    'updated_at' => $ahora,
                ]);

            if (
                $db->transStatus() === false ||
                $db->affectedRows() !== 1
            ) {
                throw new \RuntimeException(
                    'No se pudo finalizar la reparación.'
                );
            }

            $historialId = (
                new \App\Models\HistorialEstadoReparacionModel()
            )->insert([
                'reparacion_id' => $id,
                'estado_anterior' => 'EN_REPARACION',
                'estado_nuevo' => 'FINALIZADA',
                'observaciones' =>
                    'Trabajo de reparación finalizado por el personal autorizado.',
                'usuario_id' => $usuarioId,
            ], true);

            if (!$historialId || $db->transStatus() === false) {
                throw new \RuntimeException(
                    'No se pudo registrar el historial.'
                );
            }

            if (
                $db->transStatus() === false ||
                !$db->transCommit()
            ) {
                throw new \RuntimeException(
                    'No se pudo confirmar la operación.'
                );
            }

            return redirect()->to(site_url('taller/reparaciones'))
                ->with('success', 'La reparación se finalizó correctamente.');

        } catch (\Throwable $e) {
            $db->transRollback();

            log_message(
                'error',
                'Error al finalizar reparación #' . $id . ': '
                . $e->getMessage()
            );

            return redirect()->to(site_url('taller/reparaciones'))
                ->with('error', 'No fue posible finalizar la reparación.');
        }
    }

    public function entregarReparacion(int $id)
    {
        $rolId = (int) session('rol_id');
        $usuarioId = (int) session('usuario_id');

        if (
            session('logueado') !== true ||
            !in_array($rolId, [1, 3], true) ||
            $usuarioId <= 0
        ) {
            return redirect()->to(site_url('dashboard'))
                ->with('error', 'No tienes permiso para entregar reparaciones.');
        }

        $db = db_connect();

        try {
            $db->transBegin();

            // Bloquear la reparación durante toda la operación.
            $reparacion = $db->query(
                'SELECT id, estado, presupuesto_total
                FROM reparaciones
                WHERE id = ?
                FOR UPDATE',
                [$id]
            )->getRowArray();

            if (
                $reparacion === null ||
                $reparacion['estado'] !== 'FINALIZADA'
            ) {
                throw new \RuntimeException(
                    'La reparación no existe o no está finalizada.'
                );
            }

            // Trabajar con centavos para evitar errores de precisión.
            $aCentavos = static function (string $importe): int {
                if (!preg_match('/^\d+(\.\d{1,2})?$/', $importe)) {
                    throw new \RuntimeException('Importe monetario inválido.');
                }

                [$entero, $decimal] = array_pad(
                    explode('.', $importe, 2),
                    2,
                    '0'
                );

                return ((int) $entero * 100)
                    + (int) str_pad($decimal, 2, '0');
            };

            $presupuesto = $aCentavos(
                (string) $reparacion['presupuesto_total']
            );

            if ($presupuesto <= 0) {
                throw new \RuntimeException(
                    'La reparación no tiene un presupuesto válido.'
                );
            }

            // Sumar únicamente los pagos verificados.
            $pagos = $db->table('pagos_reparacion')
                ->select('monto')
                ->where('reparacion_id', $id)
                ->where('estado', 'VERIFICADO')
                ->get()
                ->getResultArray();

            $totalVerificado = 0;

            foreach ($pagos as $pago) {
                $totalVerificado += $aCentavos(
                    (string) $pago['monto']
                );
            }

            if ($totalVerificado !== $presupuesto) {
                throw new \RuntimeException(
                    'No se puede entregar una reparación sin el pago completo.'
                );
            }

            $ahora = date('Y-m-d H:i:s');

            // Registrar la entrega.
            $db->table('reparaciones')
                ->where('id', $id)
                ->where('estado', 'FINALIZADA')
                ->update([
                    'estado' => 'ENTREGADA',
                    'fecha_entrega' => $ahora,
                    'updated_at' => $ahora,
                ]);

            if (
                $db->transStatus() === false ||
                $db->affectedRows() !== 1
            ) {
                throw new \RuntimeException(
                    'No se pudo registrar la entrega.'
                );
            }

            // Guardar la transición en el historial.
            $historialId = (
                new \App\Models\HistorialEstadoReparacionModel()
            )->insert([
                'reparacion_id' => $id,
                'estado_anterior' => 'FINALIZADA',
                'estado_nuevo' => 'ENTREGADA',
                'observaciones' =>
                    'Reparación entregada con el presupuesto completamente pagado.',
                'usuario_id' => $usuarioId,
            ], true);

            if (!$historialId || $db->transStatus() === false) {
                throw new \RuntimeException(
                    'No se pudo registrar el historial de entrega.'
                );
            }

            if (
                $db->transStatus() === false ||
                !$db->transCommit()
            ) {
                throw new \RuntimeException(
                    'No se pudo confirmar la entrega.'
                );
            }

            return redirect()->to(site_url('taller/reparaciones'))
                ->with('success', 'Reparación entregada correctamente.');

        } catch (\Throwable $e) {
            $db->transRollback();

            log_message(
                'error',
                'Error al entregar reparación #' . $id . ': '
                . $e->getMessage()
            );

            return redirect()->to(site_url('taller/reparaciones'))
                ->with('error', 'No fue posible entregar la reparación.');
        }
    }

}
