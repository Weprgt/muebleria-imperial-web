<?php

namespace App\Controllers;

use App\Models\PagoReparacionModel;
use CodeIgniter\HTTP\RedirectResponse;

class VerificacionPagosReparacionController extends BaseController
{
    public function index(): string|RedirectResponse
    {
        $sesion = session();

        // Solo Administrador (1) y Ventas (2).
        if (
            $sesion->get('logueado') !== true ||
            !in_array((int) $sesion->get('rol_id'), [1, 2], true)
        ) {
            return redirect()->to(site_url('login'))
                ->with(
                    'error',
                    'No tienes permiso para consultar estos pagos.'
                );
        }

        // Consultar únicamente los pagos pendientes.
        $pagos = (new PagoReparacionModel())
            ->select(
                'pagos_reparacion.id,
                 pagos_reparacion.reparacion_id,
                 pagos_reparacion.monto,
                 pagos_reparacion.metodo_pago,
                 pagos_reparacion.referencia,
                 pagos_reparacion.estado,
                 pagos_reparacion.created_at,
                 reparaciones.estado AS estado_reparacion,
                 reparaciones.presupuesto_total,
                 reparaciones.anticipo_requerido,
                 usuarios.nombre AS nombre_cliente,
                 usuarios.apellido AS apellido_cliente'
            )
            ->join(
                'reparaciones',
                'reparaciones.id = pagos_reparacion.reparacion_id'
            )
            ->join(
                'clientes',
                'clientes.id = reparaciones.cliente_id'
            )
            ->join(
                'usuarios',
                'usuarios.id = clientes.usuario_id'
            )
            ->where('pagos_reparacion.estado', 'PENDIENTE')
            ->orderBy('pagos_reparacion.created_at', 'ASC')
            ->findAll();

        return view('ventas/pagos_reparaciones_pendientes', [
            'pagos' => $pagos,
        ]);
    }

    public function aprobar(int $pagoId): RedirectResponse
    {
        $sesion = session();

        if (
            ! $sesion->get('logueado')
            || ! in_array((int) $sesion->get('rol_id'), [1, 2], true)
        ) {
            return redirect()->to('login')
                ->with('error', 'No tienes permiso para aprobar pagos.');
        }

        $usuarioId = (int) $sesion->get('usuario_id');

        if ($usuarioId <= 0) {
            return redirect()->to('reparaciones/pagos/pendientes')
                ->with('error', 'No se pudo identificar al empleado.');
        }

        $db = db_connect();

        try {
            $db->transBegin();

            // Identificar la reparación asociada al pago.
            $pagoInicial = $db->query(
                'SELECT reparacion_id
                FROM pagos_reparacion
                WHERE id = ?',
                [$pagoId]
            )->getRowArray();

            if ($pagoInicial === null) {
                throw new \RuntimeException('El pago no existe.');
            }

            // Bloquear primero la reparación.
            $reparacion = $db->query(
                'SELECT id, estado, presupuesto_total, anticipo_requerido
                FROM reparaciones
                WHERE id = ?
                FOR UPDATE',
                [(int) $pagoInicial['reparacion_id']]
            )->getRowArray();

            if (
                $reparacion === null ||
                !in_array(
                    $reparacion['estado'],
                    ['ACEPTADA', 'FINALIZADA'],
                    true
                )
            ) {
                throw new \RuntimeException(
                    'El estado de la reparación no permite aprobar este pago.'
                );
            }

            // Bloquear después el pago.
            $pago = $db->query(
                'SELECT id, reparacion_id, monto, estado
                FROM pagos_reparacion
                WHERE id = ?
                FOR UPDATE',
                [$pagoId]
            )->getRowArray();

            if (
                $pago === null
                || (int) $pago['reparacion_id'] !== (int) $reparacion['id']
                || $pago['estado'] !== 'PENDIENTE'
            ) {
                throw new \RuntimeException(
                    'El pago ya fue procesado o no pertenece a la reparación.'
                );
            }

            $aCentavos = static function (string $importe): int {
                if (! preg_match('/^\d+(\.\d{1,2})?$/', $importe)) {
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

            $monto = $aCentavos((string) $pago['monto']);
            $presupuesto = $aCentavos(
                (string) $reparacion['presupuesto_total']
            );
            $anticipo = $aCentavos(
                (string) $reparacion['anticipo_requerido']
            );

            if ($monto <= 0 || $presupuesto <= 0 || $anticipo <= 0) {
                throw new \RuntimeException(
                    'Los importes de la reparación no son válidos.'
                );
            }

            // Comprobar los pagos ya verificados.
            $pagosAnteriores = $db->table('pagos_reparacion')
                ->select('monto')
                ->where('reparacion_id', $reparacion['id'])
                ->where('estado', 'VERIFICADO')
                ->get()
                ->getResultArray();

                $totalAnterior = 0;

                foreach ($pagosAnteriores as $anterior) {
                    $totalAnterior += $aCentavos(
                        (string) $anterior['monto']
                    );
                }

                if ($reparacion['estado'] === 'ACEPTADA') {
                    // El primer pago debe cubrir exactamente el anticipo.
                    if ($totalAnterior !== 0 || $monto !== $anticipo) {
                        throw new \RuntimeException(
                            'El anticipo no coincide con el importe requerido.'
                        );
                    }
                } else {
                    // La reparación finalizada debe tener su anticipo verificado.
                    if ($totalAnterior < $anticipo) {
                        throw new \RuntimeException(
                            'El anticipo todavía no está verificado.'
                        );
                    }

                    $saldoRestante = $presupuesto - $totalAnterior;

                    if ($saldoRestante <= 0 || $monto !== $saldoRestante) {
                        throw new \RuntimeException(
                            'El pago no coincide con el saldo pendiente.'
                        );
                    }
                }

                if ($totalAnterior + $monto > $presupuesto) {
                    throw new \RuntimeException(
                        'Los pagos superan el presupuesto de la reparación.'
                    );
                }
            $ahora = date('Y-m-d H:i:s');

            $db->table('pagos_reparacion')
                ->where('id', $pagoId)
                ->where('estado', 'PENDIENTE')
                ->update([
                    'estado'         => 'VERIFICADO',
                    'verificado_por' => $usuarioId,
                    'verificado_at'  => $ahora,
                    'updated_at'     => $ahora,
                ]);

            if (
                $db->transStatus() === false
                || $db->affectedRows() !== 1
            ) {
                throw new \RuntimeException(
                    'No se pudo aprobar el anticipo.'
                );
            }

            if (
                $db->transStatus() === false
                || ! $db->transCommit()
            ) {
                throw new \RuntimeException(
                    'No se pudo completar la transacción.'
                );
            }

            $mensaje = $reparacion['estado'] === 'ACEPTADA'
                ? 'Anticipo aprobado correctamente. La reparación está lista para iniciar.'
                : 'Saldo restante aprobado correctamente. La reparación está completamente pagada y lista para entregar.';

            return redirect()->to('reparaciones/pagos/pendientes')
                ->with('success', $mensaje);

        } catch (\Throwable $e) {
            $db->transRollback();

            log_message(
                'error',
                'Error al aprobar pago de reparación '
                . $pagoId . ': ' . $e->getMessage()
            );

            return redirect()->to('reparaciones/pagos/pendientes')
                ->with('error', 'No se pudo aprobar el pago: ' . $e->getMessage());
        }
    }

    public function rechazar(int $pagoId): RedirectResponse
    {
        $sesion = session();

        if (
            ! $sesion->get('logueado')
            || ! in_array((int) $sesion->get('rol_id'), [1, 2], true)
        ) {
            return redirect()->to('login')
                ->with('error', 'No tienes permiso para rechazar pagos.');
        }

        $usuarioId = (int) $sesion->get('usuario_id');

        if ($usuarioId <= 0) {
            return redirect()->to('reparaciones/pagos/pendientes')
                ->with('error', 'No se pudo identificar al empleado.');
        }

        $db = db_connect();

        try {
            $db->transBegin();

            // Identificar la reparación asociada.
            $pagoInicial = $db->query(
                'SELECT reparacion_id
                FROM pagos_reparacion
                WHERE id = ?',
                [$pagoId]
            )->getRowArray();

            if ($pagoInicial === null) {
                throw new \RuntimeException('El pago no existe.');
            }

            // Bloquear primero la reparación.
            $reparacion = $db->query(
                'SELECT id, estado
                FROM reparaciones
                WHERE id = ?
                FOR UPDATE',
                [(int) $pagoInicial['reparacion_id']]
            )->getRowArray();

            if (
                $reparacion === null
                || !in_array(
                    $reparacion['estado'],
                    ['ACEPTADA', 'FINALIZADA'],
                    true
                )
            ) {
                throw new \RuntimeException(
                    'El estado de la reparación no permite rechazar este pago.'
                );
            }

            // Bloquear el pago para impedir procesarlo dos veces.
            $pago = $db->query(
                'SELECT id, reparacion_id, estado
                FROM pagos_reparacion
                WHERE id = ?
                FOR UPDATE',
                [$pagoId]
            )->getRowArray();

            if (
                $pago === null
                || (int) $pago['reparacion_id'] !== (int) $reparacion['id']
                || $pago['estado'] !== 'PENDIENTE'
            ) {
                throw new \RuntimeException(
                    'El pago ya fue procesado o no pertenece a la reparación.'
                );
            }

            $ahora = date('Y-m-d H:i:s');

            $db->table('pagos_reparacion')
                ->where('id', $pagoId)
                ->where('estado', 'PENDIENTE')
                ->update([
                    'estado'         => 'RECHAZADO',
                    'verificado_por' => $usuarioId,
                    'verificado_at'  => $ahora,
                    'updated_at'     => $ahora,
                ]);

            if (
                $db->transStatus() === false
                || $db->affectedRows() !== 1
            ) {
                throw new \RuntimeException(
                    'No se pudo registrar el rechazo.'
                );
            }

            if (
                $db->transStatus() === false
                || ! $db->transCommit()
            ) {
                throw new \RuntimeException(
                    'No se pudo completar la transacción.'
                );
            }

            return redirect()->to('reparaciones/pagos/pendientes')
                ->with('success', 'Pago de reparación rechazado correctamente.');

        } catch (\Throwable $e) {
            $db->transRollback();

            log_message(
                'error',
                'Error al rechazar pago de reparación '
                . $pagoId . ': ' . $e->getMessage()
            );

            return redirect()->to('reparaciones/pagos/pendientes')
                ->with('error', 'No se pudo rechazar el pago: ' . $e->getMessage());
        }
    }


}
