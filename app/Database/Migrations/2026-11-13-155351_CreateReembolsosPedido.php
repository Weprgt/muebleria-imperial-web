<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class ReembolsoPedidoController extends BaseController
{
    public function registrar(int $pedidoId): RedirectResponse
    {
        $destino = site_url('gestion-reembolsos');

        if (
            session('logueado') !== true ||
            !in_array((int) session('rol_id'), [1, 2], true)
        ) {
            return redirect()->to(site_url('login'))
                ->with('error', 'No tienes permiso para registrar reembolsos.');
        }

        $montoTexto = trim((string) $this->request->getPost('monto'));
        $metodo = strtoupper(trim(
            (string) $this->request->getPost('metodo_reembolso')
        ));
        $referencia = trim(
            (string) $this->request->getPost('referencia')
        );
        $motivo = trim((string) $this->request->getPost('motivo'));

        // Validar un importe decimal positivo, con máximo dos decimales.
        if (
            !preg_match('/^\d{1,10}(\.\d{1,2})?$/', $montoTexto) ||
            (float) $montoTexto <= 0
        ) {
            return redirect()->to($destino)
                ->with('error', 'El monto del reembolso no es válido.');
        }

        if (!in_array($metodo, ['TRANSFERENCIA', 'DEPOSITO', 'EFECTIVO'], true)) {
            return redirect()->to($destino)
                ->with('error', 'Método de reembolso no permitido.');
        }

        if (
            mb_strlen($motivo) < 10 ||
            mb_strlen($motivo) > 2000
        ) {
            return redirect()->to($destino)
                ->with('error', 'El motivo debe contener entre 10 y 2000 caracteres.');
        }

        if (mb_strlen($referencia) > 150) {
            return redirect()->to($destino)
                ->with('error', 'La referencia es demasiado larga.');
        }

        $montoCentavos = (int) round((float) $montoTexto * 100);
        $usuarioId = (int) session('usuario_id');
        $db = db_connect();

        try {
            $db->transBegin();

            // Bloquear el pedido para serializar los reembolsos.
            $pedido = $db->query(
                'SELECT id, estado
                 FROM pedidos
                 WHERE id = ?
                 FOR UPDATE',
                [$pedidoId]
            )->getRowArray();

            if ($pedido === null || $pedido['estado'] !== 'CANCELADO') {
                throw new \RuntimeException(
                    'Solo pueden reembolsarse pedidos cancelados.'
                );
            }

            // Total de pagos verificados.
            $pagos = $db->query(
                "SELECT COALESCE(SUM(monto), 0) AS total
                 FROM pagos_pedido
                 WHERE pedido_id = ?
                   AND estado = 'VERIFICADO'",
                [$pedidoId]
            )->getRowArray();

            // Reembolsos que consumen el saldo disponible.
            $reembolsos = $db->query(
                "SELECT COALESCE(SUM(monto), 0) AS total
                 FROM reembolsos_pedido
                 WHERE pedido_id = ?
                   AND estado IN ('PENDIENTE', 'PROCESADO')",
                [$pedidoId]
            )->getRowArray();

            $pagadoCentavos = (int) round(
                (float) $pagos['total'] * 100
            );

            $reembolsadoCentavos = (int) round(
                (float) $reembolsos['total'] * 100
            );

            $disponibleCentavos =
                $pagadoCentavos - $reembolsadoCentavos;

            if (
                $disponibleCentavos <= 0 ||
                $montoCentavos > $disponibleCentavos
            ) {
                throw new \RuntimeException(
                    'El monto supera el saldo disponible para reembolso.'
                );
            }

            $ahora = date('Y-m-d H:i:s');

            $db->table('reembolsos_pedido')->insert([
                'pedido_id' => $pedidoId,
                'monto' => number_format(
                    $montoCentavos / 100,
                    2,
                    '.',
                    ''
                ),
                'metodo_reembolso' => $metodo,
                'referencia' => $referencia !== '' ? $referencia : null,
                'motivo' => $motivo,
                'estado' => 'PENDIENTE',
                'registrado_por' => $usuarioId,
                'procesado_por' => null,
                'procesado_at' => null,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);

            if (
                $db->transStatus() === false ||
                $db->affectedRows() !== 1
            ) {
                throw new \RuntimeException(
                    'No se pudo registrar el reembolso.'
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
                    'Reembolso registrado como pendiente. '
                    . 'Todavía no se ha confirmado la devolución del dinero.'
                );

        } catch (\Throwable $e) {
            $db->transRollback();

            log_message(
                'error',
                'Error al registrar reembolso del pedido '
                . $pedidoId . ': ' . $e->getMessage()
            );

            return redirect()->to($destino)
                ->with('error', 'No se pudo registrar: ' . $e->getMessage());
        }
    }
}
