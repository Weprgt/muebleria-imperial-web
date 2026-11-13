<?php

namespace App\Controllers;

use App\Models\PagoPedidoModel;
use CodeIgniter\HTTP\RedirectResponse;

class VerificacionPagosController extends BaseController
{
    public function index(): string|RedirectResponse
    {
        // Segunda comprobación de autorización.
        // La ruta también estará protegida por role:1,2.
        $sesion = session();

        if (
            ! $sesion->get('logueado')
            || ! in_array((int) $sesion->get('rol_id'), [1, 2], true)
        ) {
            return redirect()->to('login')
                ->with('error', 'No tienes permiso para consultar estos pagos.');
        }

        $pagoModel = new PagoPedidoModel();

        // Consultar únicamente pagos pendientes.
        $pagos = $pagoModel
            ->select(
                'pagos_pedido.id,
                 pagos_pedido.pedido_id,
                 pagos_pedido.monto,
                 pagos_pedido.metodo_pago,
                 pagos_pedido.referencia,
                 pagos_pedido.estado,
                 pagos_pedido.created_at,
                 pedidos.total AS total_pedido,
                 pedidos.anticipo_requerido'
            )
            ->join('pedidos', 'pedidos.id = pagos_pedido.pedido_id')
            ->where('pagos_pedido.estado', 'PENDIENTE')
            ->orderBy('pagos_pedido.created_at', 'ASC')
            ->findAll();

        return view('ventas/pagos_pendientes', [
            'pagos' => $pagos,
        ]);
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
            return redirect()->to('pagos/pendientes')
                ->with('error', 'No se pudo identificar al empleado.');
        }

        $db = db_connect();

        try {
            $db->transBegin();

            // Identificar el pedido antes de adquirir bloqueos.
            $pagoInicial = $db->query(
                'SELECT pedido_id
                FROM pagos_pedido
                WHERE id = ?',
                [$pagoId]
            )->getRowArray();

            if ($pagoInicial === null) {
                throw new \RuntimeException('El pago no existe.');
            }

            // Bloquear primero el pedido.
            $pedido = $db->query(
                'SELECT id, estado
                FROM pedidos
                WHERE id = ?
                FOR UPDATE',
                [(int) $pagoInicial['pedido_id']]
            )->getRowArray();

            if ($pedido === null) {
                throw new \RuntimeException('El pedido no existe.');
            }

            if (!in_array(
                $pedido['estado'],
                [
                    'PENDIENTE_ANTICIPO',
                    'CONFIRMADO',
                    'EN_PREPARACION',
                ],
                true
            )) {
                    throw new \RuntimeException(
                    'El estado del pedido no permite rechazar pagos.'
                );
            }

            // Bloquear después el pago.
            $pago = $db->query(
                'SELECT id, pedido_id, estado
                FROM pagos_pedido
                WHERE id = ?
                FOR UPDATE',
                [$pagoId]
            )->getRowArray();

            if (
                $pago === null
                || (int) $pago['pedido_id'] !== (int) $pedido['id']
                || $pago['estado'] !== 'PENDIENTE'
            ) {
                throw new \RuntimeException(
                    'Este pago ya fue procesado o no pertenece al pedido.'
                );
            }

            $ahora = date('Y-m-d H:i:s');

            $db->table('pagos_pedido')
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

            return redirect()->to('pagos/pendientes')
                ->with('success', 'Pago rechazado correctamente.');

        } catch (\Throwable $e) {
            $db->transRollback();

            log_message(
                'error',
                'Error al rechazar pago ' . $pagoId . ': '
                . $e->getMessage()
            );

            return redirect()->to('pagos/pendientes')
                ->with(
                    'error',
                    'No se pudo rechazar el pago: ' . $e->getMessage()
                );
        }
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
            return redirect()->to('pagos/pendientes')
                ->with('error', 'No se pudo identificar al empleado.');
        }

        $db = db_connect();
        $db->transBegin();

        try {
            // Identificar el pedido al que pertenece el pago.
            $pagoInicial = $db->query(
                'SELECT pedido_id FROM pagos_pedido WHERE id = ?',
                [$pagoId]
            )->getRowArray();

            if ($pagoInicial === null) {
                throw new \RuntimeException('El pago no existe.');
            }

            // Bloquear primero el pedido para serializar aprobaciones.
            $pedido = $db->query(
                'SELECT id, estado, total, anticipo_requerido
                FROM pedidos
                WHERE id = ?
                FOR UPDATE',
                [(int) $pagoInicial['pedido_id']]
            )->getRowArray();

            if ($pedido === null) {
                throw new \RuntimeException('El pedido no existe.');
            }

            $estadosPermitidos = [
                'PENDIENTE_ANTICIPO',
                'CONFIRMADO',
                'EN_PREPARACION',
            ];

            if (!in_array($pedido['estado'], $estadosPermitidos, true)) {
                throw new \RuntimeException(
                    'El pedido no admite la aprobación de pagos.'
                );
            }

            // Bloquear el pago y comprobar su estado actual.
            $pago = $db->query(
                'SELECT id, pedido_id, monto, estado
                FROM pagos_pedido
                WHERE id = ?
                FOR UPDATE',
                [$pagoId]
            )->getRowArray();

            if (
                $pago === null
                || (int) $pago['pedido_id'] !== (int) $pedido['id']
                || $pago['estado'] !== 'PENDIENTE'
            ) {
                throw new \RuntimeException(
                    'El pago ya fue procesado o no pertenece al pedido.'
                );
            }

            // Trabajar en centavos para evitar errores de redondeo.
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

            $montoActual = $aCentavos((string) $pago['monto']);
            $anticipoRequerido = $aCentavos(
                (string) $pedido['anticipo_requerido']
            );

            if ($montoActual <= 0) {
                throw new \RuntimeException('El monto del pago no es válido.');
            }

            // Sumar los pagos verificados anteriormente.
            $pagosVerificados = $db->table('pagos_pedido')
                ->select('monto')
                ->where('pedido_id', $pedido['id'])
                ->where('estado', 'VERIFICADO')
                ->get()
                ->getResultArray();

            $totalVerificado = $montoActual;

            foreach ($pagosVerificados as $anterior) {
                $totalVerificado += $aCentavos(
                    (string) $anterior['monto']
                );
            }
            // Impedir que los pagos verificados superen el total del pedido.
            $totalPedido = $aCentavos((string) $pedido['total']);

            if ($totalVerificado > $totalPedido) {
                throw new \RuntimeException(
                    'El pago supera el total pendiente del pedido.'
                );
            }
            // Si se alcanza el anticipo, reservar todos los muebles.
                if (
                    $pedido['estado'] === 'PENDIENTE_ANTICIPO'
                    && $totalVerificado >= $anticipoRequerido
                ) {
                $detalles = $db->table('detalle_pedido')
                    ->select('mueble_id, cantidad')
                    ->where('pedido_id', $pedido['id'])
                    ->orderBy('mueble_id', 'ASC')
                    ->get()
                    ->getResultArray();

                if ($detalles === []) {
                    throw new \RuntimeException(
                        'El pedido no contiene muebles.'
                    );
                }

                $cantidades = [];

                foreach ($detalles as $detalle) {
                    $muebleId = (int) $detalle['mueble_id'];
                    $cantidad = (int) $detalle['cantidad'];

                    if ($cantidad <= 0) {
                        throw new \RuntimeException(
                            'El pedido contiene una cantidad inválida.'
                        );
                    }

                    $cantidades[$muebleId] =
                        ($cantidades[$muebleId] ?? 0) + $cantidad;
                }

                ksort($cantidades, SORT_NUMERIC);

                foreach ($cantidades as $muebleId => $cantidad) {
                    $mueble = $db->query(
                        'SELECT id, stock, stock_reservado
                        FROM muebles
                        WHERE id = ?
                        FOR UPDATE',
                        [$muebleId]
                    )->getRowArray();

                    if ($mueble === null) {
                        throw new \RuntimeException(
                            'Uno de los muebles ya no existe.'
                        );
                    }

                    $disponible = (int) $mueble['stock']
                        - (int) $mueble['stock_reservado'];

                    if ($disponible < $cantidad) {
                        throw new \RuntimeException(
                            'Inventario insuficiente para el mueble #'
                            . $muebleId
                        );
                    }

                    $db->table('muebles')
                        ->where('id', $muebleId)
                        ->update([
                            'stock_reservado' =>
                                (int) $mueble['stock_reservado'] + $cantidad,
                        ]);

                    if ($db->transStatus() === false) {
                        throw new \RuntimeException(
                            'No se pudo reservar el inventario.'
                        );
                    }
                }

                $db->table('pedidos')
                    ->where('id', $pedido['id'])
                    ->where('estado', 'PENDIENTE_ANTICIPO')
                    ->update([
                        'estado' => 'CONFIRMADO',
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                if ($db->transStatus() === false || $db->affectedRows() !== 1) {
                    throw new \RuntimeException(
                        'No se pudo confirmar el pedido.'
                    );
                }
            }

            // Registrar quién aprobó el pago.
            $ahora = date('Y-m-d H:i:s');

            $db->table('pagos_pedido')
                ->where('id', $pagoId)
                ->where('estado', 'PENDIENTE')
                ->update([
                    'estado'         => 'VERIFICADO',
                    'verificado_por' => $usuarioId,
                    'verificado_at'  => $ahora,
                    'updated_at'     => $ahora,
                ]);

            if ($db->transStatus() === false || $db->affectedRows() !== 1) {
                throw new \RuntimeException(
                    'No se pudo registrar la aprobación.'
                );
            }

            if ($db->transStatus() === false || ! $db->transCommit()) {
                throw new \RuntimeException(
                    'No se pudo completar la transacción.'
                );
            }

            $mensaje = match (true) {
                $pedido['estado'] === 'EN_PREPARACION' =>
                    'Pago aprobado correctamente. El pedido continúa en preparación.',

                $pedido['estado'] === 'CONFIRMADO' =>
                    'Abono aprobado correctamente. El inventario permanece reservado.',

                $totalVerificado >= $anticipoRequerido =>
                    'Pago aprobado. Pedido confirmado e inventario reservado.',

                default =>
                    'Pago aprobado. El pedido sigue pendiente de completar el anticipo.',
            };

        return redirect()->to('pagos/pendientes')
            ->with('success', $mensaje);

        } catch (\Throwable $e) {
            $db->transRollback();

            log_message(
                'error',
                'Error al aprobar pago ' . $pagoId . ': ' . $e->getMessage()
            );

            return redirect()->to('pagos/pendientes')
                ->with('error', 'No se pudo aprobar el pago: ' . $e->getMessage());
        }
    }
}
