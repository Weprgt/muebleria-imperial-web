<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use CodeIgniter\HTTP\RedirectResponse;
use Throwable;

class SolicitudCancelacionController extends BaseController
{
    public function solicitar(int $pedidoId): RedirectResponse
    {
        $destino = site_url('mis-pedidos');

        if (
            session('logueado') !== true ||
            (int) session('rol_id') !== 4
        ) {
            return redirect()->to(site_url('login'))
                ->with('error', 'Debes ingresar como cliente.');
        }

        $usuarioId = (int) session('usuario_id');

        $cliente = (new ClienteModel())
            ->where('usuario_id', $usuarioId)
            ->first();

        if (!$cliente) {
            return redirect()->to($destino)
                ->with('error', 'No se encontró tu perfil de cliente.');
        }

        $motivo = trim((string) $this->request->getPost('motivo'));

        if (mb_strlen($motivo) < 10 || mb_strlen($motivo) > 2000) {
            return redirect()->to($destino)
                ->with('error', 'El motivo debe tener entre 10 y 2000 caracteres.');
        }

        $db = db_connect();

        try {
            $db->transBegin();

            // Bloquear el pedido antes de consultar las solicitudes.
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
                    'Este pedido no te pertenece.'
                );
            }

            if ($pedido['estado'] !== 'EN_PREPARACION') {
                throw new \RuntimeException(
                    'Solo puedes solicitar la cancelación durante la preparación.'
                );
            }

            // Evitar solicitudes pendientes duplicadas.
            $existePendiente = $db->table('solicitudes_cancelacion')
                ->where('pedido_id', $pedidoId)
                ->where('estado', 'PENDIENTE')
                ->countAllResults();

            if ($existePendiente > 0) {
                throw new \RuntimeException(
                    'Este pedido ya tiene una solicitud pendiente.'
                );
            }

            $ahora = date('Y-m-d H:i:s');

            $db->table('solicitudes_cancelacion')->insert([
                'pedido_id'               => $pedidoId,
                'solicitado_por'           => $usuarioId,
                'motivo'                  => $motivo,
                'estado'                  => 'PENDIENTE',
                'resuelto_por'            => null,
                'observacion_resolucion'  => null,
                'resuelto_at'             => null,
                'created_at'              => $ahora,
                'updated_at'              => $ahora,
            ]);

            if (
                $db->transStatus() === false ||
                $db->affectedRows() !== 1
            ) {
                throw new \RuntimeException(
                    'No se pudo registrar la solicitud.'
                );
            }

            if (!$db->transCommit()) {
                throw new \RuntimeException(
                    'No se pudo confirmar la operación.'
                );
            }

            return redirect()->to($destino)
                ->with(
                    'success',
                    'Solicitud registrada. El pedido continuará en preparación hasta que sea revisada.'
                );

        } catch (Throwable $e) {
            $db->transRollback();

            log_message(
                'error',
                'Error al solicitar cancelación del pedido '
                . $pedidoId . ': ' . $e->getMessage()
            );

            return redirect()->to($destino)
                ->with('error', $e->getMessage());
        }
    }
}
