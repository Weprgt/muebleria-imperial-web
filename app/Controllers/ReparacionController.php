<?php

namespace App\Controllers;

use App\Models\ClienteModel;

class ReparacionController extends BaseController
{
    public function nueva()
    {
        // Comprobar que el usuario sea un cliente autenticado.
        if (
            session('logueado') !== true ||
            (int) session('rol_id') !== 4
        ) {
            return redirect()->to(site_url('login'))
                ->with('error', 'Debes ingresar como cliente.');
        }

        // Buscar el perfil asociado al usuario autenticado.
        $cliente = (new ClienteModel())
            ->where('usuario_id', session('usuario_id'))
            ->first();

        if (!$cliente) {
            return redirect()->to(site_url('cliente'))
                ->with('error', 'No se encontró tu perfil de cliente.');
        }

        return view('reparaciones/nueva');
    }

    public function guardar()
    {
        if (
            session('logueado') !== true ||
            (int) session('rol_id') !== 4
        ) {
            return redirect()->to(site_url('login'));
        }

        $reglas = [
            'nombre_mueble' => 'required|min_length[3]|max_length[150]',
            'descripcion_problema' => 'required|min_length[10]',
        ];

        $reglas['fotografia'] = [
            'label' => 'Fotografía del mueble',
            'rules' => [
                'max_size[fotografia,5120]',
                'is_image[fotografia]',
                'mime_in[fotografia,image/jpeg,image/png,image/webp]',
                'ext_in[fotografia,jpg,jpeg,png,webp]',
            ],
        ];

        if (!$this->validate($reglas)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $cliente = (new \App\Models\ClienteModel())
            ->where('usuario_id', session('usuario_id'))
            ->first();

        if (!$cliente) {
            return redirect()->to(site_url('cliente'))
                ->with('error', 'No se encontró tu perfil de cliente.');
        }

        $db = \Config\Database::connect();

        $nombreFotografia = null;
        $rutaFotografia = null;
        $archivo = $this->request->getFile('fotografia');

        if (
            $archivo !== null &&
            $archivo->getError() !== UPLOAD_ERR_NO_FILE &&
            !$archivo->isValid()
        ) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'La fotografía no se pudo subir correctamente.');
        }

        if ($archivo !== null && $archivo->isValid()) {
            $nombreFotografia = $archivo->getRandomName();

            $directorio = WRITEPATH . 'uploads/reparaciones/';

            if (!is_dir($directorio) && !mkdir($directorio, 0750, true)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'No se pudo preparar el almacenamiento de fotografías.');
            }

            try {
                $archivo->move($directorio, $nombreFotografia);
            } catch (\Throwable $e) {
                log_message('error', 'Error al guardar fotografía: ' . $e->getMessage());

                return redirect()->back()
                    ->withInput()
                    ->with('error', 'No se pudo guardar la fotografía.');
            }

            $rutaFotografia = 'uploads/reparaciones/' . $nombreFotografia;
        }

        $db->transBegin();


        try {
            $reparacionModel = new \App\Models\ReparacionModel();
            $detalleModel = new \App\Models\DetalleReparacionModel();
            $historialModel = new \App\Models\HistorialEstadoReparacionModel();

            $reparacionId = $reparacionModel->insert([
                'cliente_id' => $cliente['id'],
                'canal' => 'WEB',
                'estado' => 'SOLICITADA',
            ], true);

            if (!$reparacionId) {
                throw new \RuntimeException('No se pudo registrar la reparación.');
            }

            $detalleId = $detalleModel->insert([
                'reparacion_id' => $reparacionId,
                'nombre_mueble' => trim((string) $this->request->getPost('nombre_mueble')),
                'descripcion_problema' => trim((string) $this->request->getPost('descripcion_problema')),
                'fotografia' => $rutaFotografia,
                'cantidad' => 1,
            ], true);

            if (!$detalleId) {
                throw new \RuntimeException('No se pudo registrar el mueble.');
            }

            $historialId = $historialModel->insert([
                'reparacion_id' => $reparacionId,
                'estado_anterior' => null,
                'estado_nuevo' => 'SOLICITADA',
                'observaciones' => 'Solicitud registrada por el cliente.',
                'usuario_id' => session('usuario_id'),
            ], true);

            if (!$historialId || $db->transStatus() === false) {
                throw new \RuntimeException('No se pudo registrar el historial.');
            }

            if ($db->transCommit() === false) {
                throw new \RuntimeException('No se pudo confirmar la transacción.');
            }

            return redirect()->to(site_url('mis-reparaciones'))
                ->with('success', 'Tu solicitud de reparación fue registrada correctamente.');

            } catch (\Throwable $e) {
                $db->transRollback();

                if ($rutaFotografia !== null) {
                    $archivoGuardado = WRITEPATH . $rutaFotografia;

                    if (is_file($archivoGuardado) && !unlink($archivoGuardado)) {
                        log_message('error', 'No se pudo eliminar la fotografía: ' . $archivoGuardado);
                    }
                }

            log_message('error', 'Error al registrar reparación: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'No fue posible registrar la solicitud. Inténtalo nuevamente.');
        }
    }

    public function misReparaciones()
    {
        if (
            session('logueado') !== true ||
            (int) session('rol_id') !== 4
        ) {
            return redirect()->to(site_url('login'));
        }

        $cliente = (new \App\Models\ClienteModel())
            ->where('usuario_id', session('usuario_id'))
            ->first();

        if (!$cliente) {
            return redirect()->to(site_url('cliente'))
                ->with('error', 'No se encontró tu perfil de cliente.');
        }

        $reparaciones = (new \App\Models\ReparacionModel())
            ->select('reparaciones.*')
            ->select(
                "(SELECT estado
                FROM pagos_reparacion
                WHERE pagos_reparacion.reparacion_id = reparaciones.id
                ORDER BY pagos_reparacion.id DESC
                LIMIT 1) AS estado_ultimo_pago",
                false
            )
            ->select(
                "(SELECT COUNT(*)
                FROM pagos_reparacion
                WHERE pagos_reparacion.reparacion_id = reparaciones.id
                AND pagos_reparacion.estado IN ('PENDIENTE', 'VERIFICADO')
                ) AS pagos_activos",
                false
            )
            ->select(
                "(SELECT COALESCE(SUM(monto), 0)
                FROM pagos_reparacion
                WHERE pagos_reparacion.reparacion_id = reparaciones.id
                AND pagos_reparacion.estado = 'VERIFICADO'
                ) AS total_verificado",
                false
            )
            ->select(
                "(SELECT GREATEST(
                    COALESCE(reparaciones.presupuesto_total, 0)
                    - COALESCE(SUM(monto), 0),
                    0
                )
                FROM pagos_reparacion
                WHERE pagos_reparacion.reparacion_id = reparaciones.id
                AND pagos_reparacion.estado = 'VERIFICADO'
                ) AS saldo_pendiente",
                false
            )
            ->select(
                "(SELECT COUNT(*)
                FROM pagos_reparacion
                WHERE pagos_reparacion.reparacion_id = reparaciones.id
                AND pagos_reparacion.estado = 'PENDIENTE'
                ) AS pagos_pendientes",
                false
            )
            ->where('reparaciones.cliente_id', $cliente['id'])
            ->orderBy('reparaciones.id', 'DESC')
            ->findAll();

        return view('reparaciones/mis_reparaciones', [
            'reparaciones' => $reparaciones,
        ]);
    }

    public function aceptarPresupuesto(int $id)
    {
        if (
            session('logueado') !== true ||
            (int) session('rol_id') !== 4
        ) {
            return redirect()->to(site_url('login'))
                ->with('error', 'Debes ingresar como cliente.');
        }

        $cliente = (new ClienteModel())
            ->where('usuario_id', session('usuario_id'))
            ->first();

        if (!$cliente) {
            return redirect()->to(site_url('mis-reparaciones'))
                ->with('error', 'No se encontró tu perfil de cliente.');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $reparacion = $db->table('reparaciones')
                ->where('id', $id)
                ->where('cliente_id', $cliente['id'])
                ->get()
                ->getRowArray();

            if (!$reparacion || $reparacion['estado'] !== 'PRESUPUESTADA') {
                throw new \RuntimeException(
                    'La reparación no existe o no está pendiente de aceptación.'
                );
            }

            if (
                $reparacion['presupuesto_total'] === null ||
                $reparacion['anticipo_requerido'] === null ||
                (float) $reparacion['presupuesto_total'] <= 0
            ) {
                throw new \RuntimeException(
                    'La reparación no tiene un presupuesto válido.'
                );
            }

            $actualizada = $db->table('reparaciones')
                ->where('id', $id)
                ->where('cliente_id', $cliente['id'])
                ->where('estado', 'PRESUPUESTADA')
                ->update([
                    'estado' => 'ACEPTADA',
                    'decision_cliente_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            if (!$actualizada || $db->affectedRows() !== 1) {
                throw new \RuntimeException(
                    'No se pudo aceptar el presupuesto.'
                );
            }

            $historialId = (new \App\Models\HistorialEstadoReparacionModel())
                ->insert([
                    'reparacion_id' => $id,
                    'estado_anterior' => 'PRESUPUESTADA',
                    'estado_nuevo' => 'ACEPTADA',
                    'observaciones' => 'Presupuesto aceptado por el cliente.',
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

            return redirect()->to(site_url('mis-reparaciones'))
                ->with('success', 'Has aceptado el presupuesto. Ahora debes registrar el anticipo para su verificación.');

        } catch (\Throwable $e) {
            $db->transRollback();

            log_message(
                'error',
                'Error al aceptar presupuesto de reparación: ' . $e->getMessage()
            );

            return redirect()->to(site_url('mis-reparaciones'))
                ->with('error', 'No fue posible aceptar el presupuesto.');
        }
    }

}
