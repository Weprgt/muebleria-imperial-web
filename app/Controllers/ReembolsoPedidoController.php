<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class ReembolsoPedidoController extends BaseController
{
    public function index()
    {
        $db = db_connect();

        $pedidos = $db->query(
            "SELECT
                p.id,
                p.total,
                p.estado,
                COALESCE((
                    SELECT SUM(pp.monto)
                    FROM pagos_pedido pp
                    WHERE pp.pedido_id = p.id
                      AND pp.estado = 'VERIFICADO'
                ), 0) AS total_pagado,
                COALESCE((
                    SELECT SUM(r.monto)
                    FROM reembolsos_pedido r
                    WHERE r.pedido_id = p.id
                      AND r.estado IN ('PENDIENTE', 'PROCESADO')
                ), 0) AS total_comprometido
             FROM pedidos p
             WHERE p.estado = 'CANCELADO'
             ORDER BY p.id DESC"
        )->getResultArray();

        
        $reembolsos = $db->query(
            "SELECT
                r.id,
                r.pedido_id,
                r.monto,
                r.metodo_reembolso,
                r.referencia,
                r.motivo,
                r.estado,
                r.registrado_por,
                r.procesado_por,
                r.procesado_at,
                r.created_at,
                u.nombre AS registrado_nombre
            FROM reembolsos_pedido r
            INNER JOIN usuarios u
                ON u.id = r.registrado_por
            ORDER BY
                CASE WHEN r.estado = 'PENDIENTE' THEN 0 ELSE 1 END,
                r.id DESC"
        )->getResultArray();

        return view('gestion_reembolsos/index', [
            'pedidos'    => $pedidos,
            'reembolsos' => $reembolsos,
        ]);

    }

    public function registrar(int $pedidoId): RedirectResponse
    {
        $destino = site_url('gestion-reembolsos');

        $montoTexto = trim(
            (string) $this->request->getPost('monto')
        );

        $metodo = strtoupper(trim(
            (string) $this->request->getPost('metodo_reembolso')
        ));

        $referencia = trim(
            (string) $this->request->getPost('referencia')
        );

        $motivo = trim(
            (string) $this->request->getPost('motivo')
        );

        if (
            !preg_match('/^\d{1,10}(\.\d{1,2})?$/', $montoTexto)
            || (float) $montoTexto <= 0
        ) {
            return redirect()->to($destino)
                ->with('error', 'El monto del reembolso no es válido.');
        }

        if (
            !in_array(
                $metodo,
                ['TRANSFERENCIA', 'DEPOSITO', 'EFECTIVO'],
                true
            )
        ) {
            return redirect()->to($destino)
                ->with('error', 'Método de reembolso no permitido.');
        }

        if (
            mb_strlen($motivo) < 10
            || mb_strlen($motivo) > 2000
        ) {
            return redirect()->to($destino)
                ->with('error', 'El motivo debe contener entre 10 y 2000 caracteres.');
        }

        if (mb_strlen($referencia) > 150) {
            return redirect()->to($destino)
                ->with('error', 'La referencia es demasiado larga.');
        }

        $montoCentavos = (int) round(
            (float) $montoTexto * 100
        );

        $usuarioId = (int) session('usuario_id');

        if ($usuarioId <= 0) {
            return redirect()->to(site_url('login'))
                ->with('error', 'Debes iniciar sesión.');
        }

        $db = db_connect();

        try {
            $db->transBegin();

            $pedido = $db->query(
                'SELECT id, estado
                 FROM pedidos
                 WHERE id = ?
                 FOR UPDATE',
                [$pedidoId]
            )->getRowArray();

            if (
                $pedido === null
                || $pedido['estado'] !== 'CANCELADO'
            ) {
                throw new \RuntimeException(
                    'Solo pueden reembolsarse pedidos cancelados.'
                );
            }

            $pagos = $db->query(
                "SELECT COALESCE(SUM(monto), 0) AS total
                 FROM pagos_pedido
                 WHERE pedido_id = ?
                   AND estado = 'VERIFICADO'",
                [$pedidoId]
            )->getRowArray();

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

            $comprometidoCentavos = (int) round(
                (float) $reembolsos['total'] * 100
            );

            $disponibleCentavos =
                $pagadoCentavos - $comprometidoCentavos;

            if (
                $disponibleCentavos <= 0
                || $montoCentavos > $disponibleCentavos
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
                'referencia' => $referencia !== ''
                    ? $referencia
                    : null,
                'motivo' => $motivo,
                'estado' => 'PENDIENTE',
                'registrado_por' => $usuarioId,
                'procesado_por' => null,
                'procesado_at' => null,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);

            if (
                $db->transStatus() === false
                || $db->affectedRows() !== 1
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
                    'Reembolso registrado como pendiente.'
                );

        } catch (\Throwable $e) {
            $db->transRollback();

            log_message(
                'error',
                'Error en reembolso del pedido '
                . $pedidoId . ': ' . $e->getMessage()
            );

            return redirect()->to($destino)
                ->with(
                    'error',
                    'No se pudo registrar el reembolso. '
                    . 'Verifica el estado del pedido y el saldo disponible.'
                );
        }
    }

    public function procesar(int $reembolsoId): RedirectResponse
    {
        $destino = site_url('gestion-reembolsos');
        $usuarioId = (int) session('usuario_id');

        if ($usuarioId <= 0) {
            return redirect()->to(site_url('login'));
        }

        $db = db_connect();

        try {
            $db->transBegin();

            // Localizar el pedido asociado al reembolso.
            $registro = $db->table('reembolsos_pedido')
                ->select('pedido_id')
                ->where('id', $reembolsoId)
                ->get()
                ->getRowArray();

            if ($registro === null) {
                throw new \RuntimeException('Reembolso no encontrado.');
            }

            // Bloquear primero el pedido, como en registrar().
            $pedido = $db->query(
                'SELECT id, estado
                FROM pedidos
                WHERE id = ?
                FOR UPDATE',
                [(int) $registro['pedido_id']]
            )->getRowArray();

            if ($pedido === null || $pedido['estado'] !== 'CANCELADO') {
                throw new \RuntimeException('El pedido no está cancelado.');
            }

            // Bloquear el reembolso antes de cambiar su estado.
            $reembolso = $db->query(
                'SELECT id, estado
                FROM reembolsos_pedido
                WHERE id = ?
                FOR UPDATE',
                [$reembolsoId]
            )->getRowArray();

            if ($reembolso === null || $reembolso['estado'] !== 'PENDIENTE') {
                throw new \RuntimeException(
                    'Solo se pueden procesar reembolsos pendientes.'
                );
            }

            $db->table('reembolsos_pedido')
                ->where('id', $reembolsoId)
                ->where('estado', 'PENDIENTE')
                ->update([
                    'estado' => 'PROCESADO',
                    'procesado_por' => $usuarioId,
                    'procesado_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            if (
                $db->transStatus() === false ||
                $db->affectedRows() !== 1
            ) {
                throw new \RuntimeException(
                    'No se pudo procesar el reembolso.'
                );
            }

            if (!$db->transCommit()) {
                throw new \RuntimeException(
                    'No se pudo confirmar la transacción.'
                );
            }

            return redirect()->to($destino)
                ->with('success', 'Reembolso marcado como procesado.');

        } catch (\Throwable $e) {
            $db->transRollback();

            log_message('error', 'Error procesando reembolso: ' . $e->getMessage());

            return redirect()->to($destino)
                ->with('error', 'No se pudo procesar el reembolso.');
        }
    }

    public function rechazar(int $reembolsoId): RedirectResponse
    {
        $destino = site_url('gestion-reembolsos');
        $usuarioId = (int) session('usuario_id');

        if ($usuarioId <= 0) {
            return redirect()->to(site_url('login'));
        }

        $db = db_connect();

        try {
            $db->transBegin();

            $registro = $db->table('reembolsos_pedido')
                ->select('pedido_id')
                ->where('id', $reembolsoId)
                ->get()
                ->getRowArray();

            if ($registro === null) {
                throw new \RuntimeException('Reembolso no encontrado.');
            }

            $pedido = $db->query(
                'SELECT id
                FROM pedidos
                WHERE id = ?
                FOR UPDATE',
                [(int) $registro['pedido_id']]
            )->getRowArray();

            if ($pedido === null) {
                throw new \RuntimeException('Pedido no encontrado.');
            }

            $reembolso = $db->query(
                'SELECT id, estado
                FROM reembolsos_pedido
                WHERE id = ?
                FOR UPDATE',
                [$reembolsoId]
            )->getRowArray();

            if ($reembolso === null || $reembolso['estado'] !== 'PENDIENTE') {
                throw new \RuntimeException(
                    'Solo se pueden rechazar reembolsos pendientes.'
                );
            }

            $db->table('reembolsos_pedido')
                ->where('id', $reembolsoId)
                ->where('estado', 'PENDIENTE')
                ->update([
                    'estado' => 'RECHAZADO',
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            if (
                $db->transStatus() === false ||
                $db->affectedRows() !== 1
            ) {
                throw new \RuntimeException(
                    'No se pudo rechazar el reembolso.'
                );
            }

            if (!$db->transCommit()) {
                throw new \RuntimeException(
                    'No se pudo confirmar la transacción.'
                );
            }

            return redirect()->to($destino)
                ->with('success', 'Reembolso rechazado correctamente.');

        } catch (\Throwable $e) {
            $db->transRollback();

            log_message('error', 'Error rechazando reembolso: ' . $e->getMessage());

            return redirect()->to($destino)
                ->with('error', 'No se pudo rechazar el reembolso.');
        }
    }

}
