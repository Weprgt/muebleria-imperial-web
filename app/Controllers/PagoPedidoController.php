<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\PedidoModel;
use App\Models\PagoPedidoModel;
use Throwable;

class PagoPedidoController extends BaseController
{
    public function registrar()
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
            return redirect()->to(site_url('mis-pedidos'))
                ->with('error', 'No se encontro tu perfil de cliente.');
        }

        $pedidoId = filter_var(
            $this->request->getPost('pedido_id'),
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        if (!$pedidoId) {
            return redirect()->to(site_url('mis-pedidos'))
                ->with('error', 'Pedido invalido.');
        }

        $db = db_connect();
        $db->transBegin();

        try {
            // Bloquear el pedido mientras se registra el pago.
            $pedido = $db->table('pedidos')
                ->where('id', $pedidoId)
                ->where('cliente_id', $cliente['id'])
                ->getCompiledSelect();

            $resultado = $db->query(
                $pedido . ' FOR UPDATE'
            )->getRowArray();

            if (!$resultado) {
                throw new \RuntimeException(
                    'El pedido no existe o no te pertenece.'
                );
            }

            $estadosPermitidos = [
                'PENDIENTE_ANTICIPO',
                'CONFIRMADO',
                'EN_PREPARACION',
            ];

            if (!in_array($resultado['estado'], $estadosPermitidos, true)) {
                throw new \RuntimeException(
                    'Este pedido no admite nuevos pagos.'
                );
            }

            // Validar el monto con dos decimales.
            $monto = trim((string) $this->request->getPost('monto'));

            if (!preg_match('/^\d{1,8}(\.\d{1,2})?$/', $monto)) {
                throw new \RuntimeException(
                    'El monto del pago no es valido.'
                );
            }

            $partes = explode('.', $monto);
            $montoCentavos =
                ((int) $partes[0] * 100) +
                (int) str_pad($partes[1] ?? '0', 2, '0');

            if ($montoCentavos <= 0) {
                throw new \RuntimeException(
                    'El monto debe ser mayor que cero.'
                );
            }

            $metodo = trim((string) $this->request->getPost('metodo_pago'));

            $metodosPermitidos = [
                'TRANSFERENCIA',
                'DEPOSITO',
                'EFECTIVO',
            ];

            if (!in_array($metodo, $metodosPermitidos, true)) {
                throw new \RuntimeException(
                    'Metodo de pago no permitido.'
                );
            }

            $referencia = trim(
                (string) $this->request->getPost('referencia')
            );

            if (mb_strlen($referencia) > 150) {
                throw new \RuntimeException(
                    'La referencia es demasiado larga.'
                );
            }

            if (
                in_array($metodo, ['TRANSFERENCIA', 'DEPOSITO'], true)
                && $referencia === ''
            ) {
                throw new \RuntimeException(
                    'Debes indicar la referencia del pago.'
                );
            }

            // Sumar pagos pendientes y verificados.
            $pagos = $db->table('pagos_pedido')
                ->select('monto')
                ->where('pedido_id', $pedidoId)
                ->whereIn('estado', ['PENDIENTE', 'VERIFICADO'])
                ->get()
                ->getResultArray();

            $pagadoCentavos = 0;

            foreach ($pagos as $pago) {
                [$q, $c] = explode('.', (string) $pago['monto']);
                $pagadoCentavos += ((int) $q * 100) + (int) $c;
            }

            [$totalQ, $totalC] = explode(
                '.',
                (string) $resultado['total']
            );

            $totalCentavos =
                ((int) $totalQ * 100) + (int) $totalC;

            if ($montoCentavos > $totalCentavos - $pagadoCentavos) {
                throw new \RuntimeException(
                    'El pago supera el saldo disponible del pedido.'
                );
            }

            $pagoModel = new PagoPedidoModel();

            $pagoId = $pagoModel->insert([
                'pedido_id'   => $pedidoId,
                'monto'       => number_format(
                    $montoCentavos / 100, 2, '.', ''
                ),
                'metodo_pago' => $metodo,
                'referencia'  => $referencia !== '' ? $referencia : null,
                'estado'      => 'PENDIENTE',
            ]);

            if (!$pagoId || $db->transStatus() === false) {
                throw new \RuntimeException(
                    'No se pudo registrar el pago.'
                );
            }

            $db->transCommit();

            return redirect()->to(site_url('mis-pedidos'))
                ->with(
                    'success',
                    'Pago #' . $pagoId .
                    ' registrado. Pendiente de verificacion.'
                );

        } catch (Throwable $e) {
            $db->transRollback();

            log_message(
                'error',
                'Error al registrar pago: {mensaje}',
                ['mensaje' => $e->getMessage()]
            );

            return redirect()->to(site_url('cliente'))
                ->with(
                    'error',
                    $e instanceof \RuntimeException
                        ? $e->getMessage()
                        : 'No se pudo registrar el pago.'
                );
        }
    }
}
