<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class EntregaPedidoController extends BaseController
{
    public function index(): string|RedirectResponse
    {
        $sesion = session();

        if (
            ! $sesion->get('logueado')
            || ! in_array((int) $sesion->get('rol_id'), [1, 2], true)
        ) {
            return redirect()->to('login')
                ->with('error', 'No tienes permiso para consultar las entregas.');
        }

        $db = db_connect();

        $pedidos = $db->table('pedidos')
            ->select('pedidos.id, pedidos.total, pedidos.estado, pedidos.created_at')
            ->whereIn('pedidos.estado', [
                'CONFIRMADO',
                'EN_PREPARACION',
            ])
            ->orderBy('pedidos.created_at', 'ASC')
            ->get()
            ->getResultArray();

        return view('ventas/entregas_pendientes', [
            'pedidos' => $pedidos,
        ]);
    }
    public function registrar(int $pedidoId): RedirectResponse
    {
        $sesion = session();

        if (
            ! $sesion->get('logueado')
            || ! in_array((int) $sesion->get('rol_id'), [1, 2], true)
        ) {
            return redirect()->to('login')
                ->with('error', 'No tienes permiso para registrar entregas.');
        }

        $usuarioId = (int) $sesion->get('usuario_id');

        if ($usuarioId <= 0) {
            return redirect()->to('login')
                ->with('error', 'La sesión no es válida.');
        }

        $db = db_connect();
        $db->transBegin();

        try {
            // Bloquear el pedido para impedir entregas simultáneas.
            $pedido = $db->query(
                'SELECT id, total, estado
                FROM pedidos
                WHERE id = ?
                FOR UPDATE',
                [$pedidoId]
            )->getRowArray();

            if (! $pedido || $pedido['estado'] !== 'EN_PREPARACION') {
                throw new \RuntimeException(
                    'El pedido no existe o no está en preparación.'
                );
            }

            // Comprobar que todavía no se haya entregado.
            $entregaExistente = $db->table('entregas_pedido')
                ->where('pedido_id', $pedidoId)
                ->countAllResults();

            if ($entregaExistente > 0) {
                throw new \RuntimeException(
                    'Este pedido ya tiene una entrega registrada.'
                );
            }

            // Trabajar con centavos para evitar errores de decimales.
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

            $totalCentavos = $aCentavos($pedido['total']);

            $pagos = $db->table('pagos_pedido')
                ->select('monto')
                ->where('pedido_id', $pedidoId)
                ->where('estado', 'VERIFICADO')
                ->get()
                ->getResultArray();

            $pagadoCentavos = 0;

            foreach ($pagos as $pago) {
                $pagadoCentavos += $aCentavos($pago['monto']);
            }

            if ($pagadoCentavos !== $totalCentavos) {
                throw new \RuntimeException(
                    'El pedido debe estar pagado completamente antes de entregarse.'
                );
            }
            $pagosPendientes = $db->table('pagos_pedido')
                ->where('pedido_id', $pedidoId)
                ->where('estado', 'PENDIENTE')
                ->countAllResults();

            if ($pagosPendientes > 0) {
                throw new \RuntimeException(
                    'Existen pagos pendientes de verificación para este pedido.'
                );
            }

            // Obtener los muebles incluidos en el pedido.
            $detalles = $db->table('detalle_pedido')
                ->select('mueble_id, cantidad')
                ->where('pedido_id', $pedidoId)
                ->get()
                ->getResultArray();

            if (empty($detalles)) {
                throw new \RuntimeException(
                    'El pedido no tiene productos registrados.'
                );
            }

            // Agrupar cantidades por mueble.
            $cantidades = [];

            foreach ($detalles as $detalle) {
                $muebleId = (int) $detalle['mueble_id'];
                $cantidad = (int) $detalle['cantidad'];

                if ($cantidad <= 0) {
                    throw new \RuntimeException(
                        'Se encontró una cantidad inválida.'
                    );
                }

                $cantidades[$muebleId] =
                    ($cantidades[$muebleId] ?? 0) + $cantidad;
            }

            // Mantener un orden consistente al bloquear el inventario.
            ksort($cantidades, SORT_NUMERIC);

            foreach ($cantidades as $muebleId => $cantidad) {
                $mueble = $db->query(
                    'SELECT id, stock, stock_reservado
                    FROM muebles
                    WHERE id = ?
                    FOR UPDATE',
                    [$muebleId]
                )->getRowArray();

                if (
                    ! $mueble
                    || (int) $mueble['stock'] < $cantidad
                    || (int) $mueble['stock_reservado'] < $cantidad
                ) {
                    throw new \RuntimeException(
                        'El inventario del mueble #' . $muebleId
                        . ' no permite completar la entrega.'
                    );
                }

                $db->table('muebles')
                    ->where('id', $muebleId)
                    ->update([
                        'stock' => (int) $mueble['stock'] - $cantidad,
                        'stock_reservado' =>
                            (int) $mueble['stock_reservado'] - $cantidad,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                if ($db->affectedRows() !== 1) {
                    throw new \RuntimeException(
                        'No se pudo actualizar el inventario.'
                    );
                }
            }

            $ahora = date('Y-m-d H:i:s');

            // Registrar la entrega.
            $db->table('entregas_pedido')->insert([
                'pedido_id' => $pedidoId,
                'entregado_por' => $usuarioId,
                'fecha_entrega' => $ahora,
                'observaciones' => null,
                'created_at' => $ahora,
            ]);

            if ($db->affectedRows() !== 1) {
                throw new \RuntimeException(
                    'No se pudo registrar la entrega.'
                );
            }

            // Marcar el pedido como entregado.
            $db->table('pedidos')
                ->where('id', $pedidoId)
                ->where('estado', 'EN_PREPARACION')
                ->update([
                    'estado' => 'ENTREGADO',
                    'updated_at' => $ahora,
                ]);

            if ($db->affectedRows() !== 1 || $db->transStatus() === false) {
                throw new \RuntimeException(
                    'No se pudo actualizar el estado del pedido.'
                );
            }

            if ($db->transStatus() === false || ! $db->transCommit()) {
                throw new \RuntimeException(
                    'No se pudo completar la transacción de entrega.'
                );
            }

            return redirect()->to('entregas/pendientes')
                ->with('success', 'Pedido #' . $pedidoId . ' entregado correctamente.');

        } catch (\Throwable $e) {
            $db->transRollback();

            log_message(
                'error',
                'Error al registrar entrega del pedido {pedido}: {mensaje}',
                [
                    'pedido' => $pedidoId,
                    'mensaje' => $e->getMessage(),
                ]
            );

            return redirect()->to('entregas/pendientes')
                ->with('error', $e->getMessage());
        }
    }
}