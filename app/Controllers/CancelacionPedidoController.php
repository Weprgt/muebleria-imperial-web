<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use CodeIgniter\HTTP\RedirectResponse;

class CancelacionPedidoController extends BaseController
{
    public function cancelar(int $pedidoId): RedirectResponse
    {
        $sesion = session();

        if (
            $sesion->get('logueado') !== true ||
            (int) $sesion->get('rol_id') !== 4
        ) {
            return redirect()->to(site_url('login'))
                ->with('error', 'Debes ingresar como cliente.');
        }

        $usuarioId = (int) $sesion->get('usuario_id');

        $cliente = (new ClienteModel())
            ->where('usuario_id', $usuarioId)
            ->first();

        if (!$cliente) {
            return redirect()->to(site_url('mis-pedidos'))
                ->with('error', 'No se encontró tu perfil de cliente.');
        }

        $motivo = trim((string) $this->request->getPost('motivo'));

        if (mb_strlen($motivo) < 10 || mb_strlen($motivo) > 2000) {
            return redirect()->to(site_url('mis-pedidos'))
                ->with('error', 'El motivo debe contener entre 10 y 2000 caracteres.');
        }

        $db = db_connect();

        try {
            $db->transBegin();

            // Bloquear el pedido para impedir cancelaciones simultáneas.
            $pedido = $db->query(
                'SELECT id, cliente_id, estado
                 FROM pedidos
                 WHERE id = ?
                 FOR UPDATE',
                [$pedidoId]
            )->getRowArray();

            if ($pedido === null) {
                throw new \RuntimeException('El pedido no existe.');
            }

            if ((int) $pedido['cliente_id'] !== (int) $cliente['id']) {
                throw new \RuntimeException(
                    'No tienes permiso para cancelar este pedido.'
                );
            }

            if (!in_array(
                $pedido['estado'],
                ['PENDIENTE_ANTICIPO', 'CONFIRMADO'],
                true
            )) {
                throw new \RuntimeException(
                    'El estado actual del pedido no permite cancelación directa.'
                );
            }

            // Solo los pedidos confirmados tienen inventario reservado.
            if ($pedido['estado'] === 'CONFIRMADO') {
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

                foreach ($cantidades as $muebleId => $cantidad) {
                    $mueble = $db->query(
                        'SELECT id, stock_reservado
                         FROM muebles
                         WHERE id = ?
                         FOR UPDATE',
                        [$muebleId]
                    )->getRowArray();

                    if ($mueble === null) {
                        throw new \RuntimeException(
                            'Uno de los muebles no existe.'
                        );
                    }

                    if ((int) $mueble['stock_reservado'] < $cantidad) {
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
                            'No se pudo liberar la reserva.'
                        );
                    }
                }
            }

            $ahora = date('Y-m-d H:i:s');

            // Cambiar el estado del pedido.
            $db->table('pedidos')
                ->where('id', $pedidoId)
                ->where('estado', $pedido['estado'])
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

            // Registrar la cancelación aprobada directamente.
            $db->table('solicitudes_cancelacion')->insert([
                'pedido_id' => $pedidoId,
                'solicitado_por' => $usuarioId,
                'motivo' => $motivo,
                'estado' => 'APROBADA',
                'resuelto_por' => null,
                'observacion_resolucion' =>
                    'Cancelación directa antes de preparación.',
                'resuelto_at' => $ahora,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);

            if ($db->transStatus() === false) {
                throw new \RuntimeException(
                    'No se pudo registrar el historial de cancelación.'
                );
            }

            if (!$db->transCommit()) {
                throw new \RuntimeException(
                    'No se pudo confirmar la transacción.'
                );
            }

            return redirect()->to(site_url('mis-pedidos'))
                ->with(
                    'success',
                    'Pedido cancelado correctamente. '
                    . 'Los pagos realizados se conservan para gestionar '
                    . 'el reembolso correspondiente.'
                );

        } catch (\Throwable $e) {
            $db->transRollback();

            log_message(
                'error',
                'Error al cancelar pedido ' . $pedidoId . ': '
                . $e->getMessage()
            );

            return redirect()->to(site_url('mis-pedidos'))
                ->with('error', 'No se pudo cancelar: ' . $e->getMessage());
        }
    }
}

