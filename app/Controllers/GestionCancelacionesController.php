<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class GestionCancelacionesController extends BaseController
{
    public function index()
    {
        // Solo Administrador y Ventas.
        if (
            session('logueado') !== true ||
            !in_array((int) session('rol_id'), [1, 2], true)
        ) {
            return redirect()->to(site_url('login'))
                ->with('error', 'No tienes permiso para acceder.');
        }

        $db = db_connect();

        // Consultar solicitudes pendientes con datos del cliente.
        $solicitudes = $db->table('solicitudes_cancelacion sc')
            ->select(
                'sc.id,
                 sc.pedido_id,
                 sc.motivo,
                 sc.estado,
                 sc.created_at,
                 p.total,
                 p.estado AS estado_pedido,
                 u.nombre,
                 u.apellido,
                 u.correo'
            )
            ->join('pedidos p', 'p.id = sc.pedido_id')
            ->join('clientes c', 'c.id = p.cliente_id')
            ->join('usuarios u', 'u.id = c.usuario_id')
            ->where('sc.estado', 'PENDIENTE')
            ->orderBy('sc.created_at', 'ASC')
            ->get()
            ->getResultArray();

        return view('gestion_cancelaciones/index', [
            'solicitudes' => $solicitudes,
        ]);
    }

    public function aprobar(int $solicitudId): \CodeIgniter\HTTP\RedirectResponse
    {
        $destino = site_url('gestion-cancelaciones');

        if (
            session('logueado') !== true ||
            !in_array((int) session('rol_id'), [1, 2], true)
        ) {
            return redirect()->to(site_url('login'))
                ->with('error', 'No tienes permiso para aprobar cancelaciones.');
        }

        $usuarioId = (int) session('usuario_id');
        $db = db_connect();

        try {
            $db->transBegin();

            // Consultar la solicitud para identificar su pedido.
            $solicitud = $db->query(
                'SELECT id, pedido_id, estado
                FROM solicitudes_cancelacion
                WHERE id = ?',
                [$solicitudId]
            )->getRowArray();

            if ($solicitud === null) {
                throw new \RuntimeException('La solicitud no existe.');
            }

            $pedidoId = (int) $solicitud['pedido_id'];

            // Bloquear el pedido para serializar operaciones concurrentes.
            $pedido = $db->query(
                'SELECT id, estado
                FROM pedidos
                WHERE id = ?
                FOR UPDATE',
                [$pedidoId]
            )->getRowArray();

            if ($pedido === null) {
                throw new \RuntimeException('El pedido no existe.');
            }

            // Bloquear y volver a comprobar la solicitud.
            $solicitud = $db->query(
                'SELECT id, pedido_id, estado
                FROM solicitudes_cancelacion
                WHERE id = ?
                FOR UPDATE',
                [$solicitudId]
            )->getRowArray();

            if (
                $solicitud === null ||
                (int) $solicitud['pedido_id'] !== $pedidoId ||
                $solicitud['estado'] !== 'PENDIENTE'
            ) {
                throw new \RuntimeException(
                    'La solicitud ya fue resuelta o no es válida.'
                );
            }

            if ($pedido['estado'] !== 'EN_PREPARACION') {
                throw new \RuntimeException(
                    'El pedido ya no se encuentra en preparación.'
                );
            }

            // Obtener las cantidades reservadas por mueble.
            $detalles = $db->table('detalle_pedido')
                ->select('mueble_id, cantidad')
                ->where('pedido_id', $pedidoId)
                ->orderBy('mueble_id', 'ASC')
                ->get()
                ->getResultArray();

            if ($detalles === []) {
                throw new \RuntimeException(
                    'El pedido no contiene detalles.'
                );
            }

            $cantidades = [];

            foreach ($detalles as $detalle) {
                $muebleId = (int) $detalle['mueble_id'];
                $cantidad = (int) $detalle['cantidad'];

                if ($cantidad <= 0) {
                    throw new \RuntimeException(
                        'Cantidad inválida en el detalle del pedido.'
                    );
                }

                $cantidades[$muebleId] =
                    ($cantidades[$muebleId] ?? 0) + $cantidad;
            }

            ksort($cantidades, SORT_NUMERIC);

            // Liberar las reservas sin descontar el stock físico.
            foreach ($cantidades as $muebleId => $cantidad) {
                $mueble = $db->query(
                    'SELECT id, stock_reservado
                    FROM muebles
                    WHERE id = ?
                    FOR UPDATE',
                    [$muebleId]
                )->getRowArray();

                if (
                    $mueble === null ||
                    (int) $mueble['stock_reservado'] < $cantidad
                ) {
                    throw new \RuntimeException(
                        'La reserva de inventario es insuficiente.'
                    );
                }

                $db->table('muebles')
                    ->where('id', $muebleId)
                    ->where('stock_reservado >=', $cantidad)
                    ->set(
                        'stock_reservado',
                        'stock_reservado - ' . $cantidad,
                        false
                    )
                    ->update();

                if (
                    $db->transStatus() === false ||
                    $db->affectedRows() !== 1
                ) {
                    throw new \RuntimeException(
                        'No se pudo liberar la reserva de inventario.'
                    );
                }
            }

            $ahora = date('Y-m-d H:i:s');

            // Cancelar el pedido.
            $db->table('pedidos')
                ->where('id', $pedidoId)
                ->where('estado', 'EN_PREPARACION')
                ->update([
                    'estado' => 'CANCELADO',
                    'updated_at' => $ahora,
                ]);

            if (
                $db->transStatus() === false ||
                $db->affectedRows() !== 1
            ) {
                throw new \RuntimeException(
                    'No se pudo cancelar el pedido.'
                );
            }

            // Registrar quién aprobó la solicitud.
            $db->table('solicitudes_cancelacion')
                ->where('id', $solicitudId)
                ->where('estado', 'PENDIENTE')
                ->update([
                    'estado' => 'APROBADA',
                    'resuelto_por' => $usuarioId,
                    'observacion_resolucion' =>
                        'Cancelación aprobada por Administración o Ventas.',
                    'resuelto_at' => $ahora,
                    'updated_at' => $ahora,
                ]);

            if (
                $db->transStatus() === false ||
                $db->affectedRows() !== 1
            ) {
                throw new \RuntimeException(
                    'No se pudo aprobar la solicitud.'
                );
            }

            if (!$db->transCommit()) {
                throw new \RuntimeException(
                    'No se pudo confirmar la transacción.'
                );
            }

            return redirect()->to($destino)
                ->with(
                    'success',
                    'Solicitud aprobada. Pedido cancelado y reservas liberadas. '
                    . 'Los reembolsos deben gestionarse por separado.'
                );

        } catch (\Throwable $e) {
            $db->transRollback();

            log_message(
                'error',
                'Error al aprobar solicitud de cancelación '
                . $solicitudId . ': ' . $e->getMessage()
            );

            return redirect()->to($destino)
                ->with('error', 'No se pudo aprobar: ' . $e->getMessage());
        }
    }

    public function rechazar(int $solicitudId): \CodeIgniter\HTTP\RedirectResponse
    {
        $destino = site_url('gestion-cancelaciones');

        if (
            session('logueado') !== true ||
            !in_array((int) session('rol_id'), [1, 2], true)
        ) {
            return redirect()->to(site_url('login'))
                ->with('error', 'No tienes permiso para rechazar cancelaciones.');
        }

        $usuarioId = (int) session('usuario_id');
        $db = db_connect();

        try {
            $db->transBegin();

            // Identificar el pedido asociado a la solicitud.
            $solicitud = $db->query(
                'SELECT id, pedido_id
                FROM solicitudes_cancelacion
                WHERE id = ?',
                [$solicitudId]
            )->getRowArray();

            if ($solicitud === null) {
                throw new \RuntimeException('La solicitud no existe.');
            }

            // Bloquear primero el pedido.
            $pedido = $db->query(
                'SELECT id, estado
                FROM pedidos
                WHERE id = ?
                FOR UPDATE',
                [(int) $solicitud['pedido_id']]
            )->getRowArray();

            if ($pedido === null || $pedido['estado'] !== 'EN_PREPARACION') {
                throw new \RuntimeException(
                    'El pedido ya no se encuentra en preparación.'
                );
            }

            // Bloquear y comprobar el estado actual de la solicitud.
            $solicitudActual = $db->query(
                'SELECT id, pedido_id, estado
                FROM solicitudes_cancelacion
                WHERE id = ?
                FOR UPDATE',
                [$solicitudId]
            )->getRowArray();

            if (
                $solicitudActual === null ||
                (int) $solicitudActual['pedido_id'] !== (int) $pedido['id'] ||
                $solicitudActual['estado'] !== 'PENDIENTE'
            ) {
                throw new \RuntimeException(
                    'La solicitud ya fue resuelta o no es válida.'
                );
            }

            $ahora = date('Y-m-d H:i:s');

            $db->table('solicitudes_cancelacion')
                ->where('id', $solicitudId)
                ->where('estado', 'PENDIENTE')
                ->update([
                    'estado' => 'RECHAZADA',
                    'resuelto_por' => $usuarioId,
                    'observacion_resolucion' =>
                        'Solicitud rechazada por Administración o Ventas.',
                    'resuelto_at' => $ahora,
                    'updated_at' => $ahora,
                ]);

            if (
                $db->transStatus() === false ||
                $db->affectedRows() !== 1
            ) {
                throw new \RuntimeException(
                    'No se pudo rechazar la solicitud.'
                );
            }

            if (!$db->transCommit()) {
                throw new \RuntimeException(
                    'No se pudo confirmar la transacción.'
                );
            }

            return redirect()->to($destino)
                ->with(
                    'success',
                    'Solicitud rechazada. El pedido continúa en preparación.'
                );

        } catch (\Throwable $e) {
            $db->transRollback();

            log_message(
                'error',
                'Error al rechazar solicitud '
                . $solicitudId . ': ' . $e->getMessage()
            );

            return redirect()->to($destino)
                ->with('error', 'No se pudo rechazar: ' . $e->getMessage());
        }
    }

}
