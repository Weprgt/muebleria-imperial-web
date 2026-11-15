<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;
use Throwable;

class PreparacionPedidoController extends BaseController
{
    public function iniciar(int $pedidoId): RedirectResponse
    {
        // 1. Comprobar autenticación y permisos.
        if (
            session('logueado') !== true ||
            !in_array((int) session('rol_id'), [1, 2], true)
        ) {
            return redirect()->to(site_url('login'))
                ->with('error', 'No tienes permiso para preparar pedidos.');
        }

        $db = db_connect();

        try {
            // 2. Iniciar transacción.
            $db->transBegin();

            // 3. Bloquear el pedido para evitar cambios simultáneos.
            $pedido = $db->query(
                'SELECT id, estado
                 FROM pedidos
                 WHERE id = ?
                 FOR UPDATE',
                [$pedidoId]
            )->getRowArray();

            if ($pedido === null) {
                throw new \RuntimeException(
                    'El pedido no existe.'
                );
            }

            // 4. Solo los pedidos confirmados pueden prepararse.
            if ($pedido['estado'] !== 'CONFIRMADO') {
                throw new \RuntimeException(
                    'Solo puedes iniciar la preparación de pedidos confirmados.'
                );
            }

            // 5. Cambiar el estado sin modificar el inventario.
            $db->table('pedidos')
                ->where('id', $pedidoId)
                ->where('estado', 'CONFIRMADO')
                ->update([
                    'estado'      => 'EN_PREPARACION',
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            if (
                $db->transStatus() === false ||
                $db->affectedRows() !== 1
            ) {
                throw new \RuntimeException(
                    'No se pudo iniciar la preparación.'
                );
            }

            // 6. Confirmar la transacción.
            if (!$db->transCommit()) {
                throw new \RuntimeException(
                    'No se pudo confirmar la operación.'
                );
            }

            // 7. Registrar el cambio de estado en MongoDB.
            // La auditoría no debe impedir la preparación del pedido.
            try {
                $auditoria = new \App\Services\AuditoriaMongoService();

                $auditoria->registrar(
                    'PEDIDOS',
                    'PEDIDO_EN_PREPARACION',
                    [
                        'pedido_id'       => $pedidoId,
                        'estado_anterior' => 'CONFIRMADO',
                        'estado_nuevo'    => 'EN_PREPARACION',
                        'usuario_id'      => session()->get('usuario_id'),
                    ]
                );
            } catch (Throwable $e) {
                log_message(
                    'error',
                    'Error de auditoría al preparar pedido #'
                    . $pedidoId . ': ' . $e->getMessage()
                );
            }

            return redirect()->to(site_url('entregas/pendientes'))
                ->with(
                    'success',
                    'Pedido #' . $pedidoId .
                    ' enviado a preparación correctamente.'
                );

        } catch (Throwable $e) {
            $db->transRollback();

            log_message(
                'error',
                'Error al iniciar preparación del pedido '
                . $pedidoId . ': ' . $e->getMessage()
            );

            return redirect()->to(site_url('entregas/pendientes'))
                ->with('error', $e->getMessage());
        }
    }
}
