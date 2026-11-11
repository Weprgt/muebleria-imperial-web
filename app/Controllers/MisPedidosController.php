<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\PedidoModel;
use App\Models\PagoPedidoModel;

class MisPedidosController extends BaseController
{
    public function index()
    {
        // Verificar que el usuario sea un cliente autenticado.
        if (
            session('logueado') !== true ||
            (int) session('rol_id') !== 4
        ) {
            return redirect()->to(site_url('login'))
                ->with('error', 'Debes ingresar como cliente.');
        }

        // Obtener el perfil del cliente autenticado.
        $cliente = (new ClienteModel())
            ->where('usuario_id', session('usuario_id'))
            ->first();

        if (!$cliente) {
            return redirect()->to(site_url('cliente'))
                ->with(
                    'error',
                    'No se encontro tu perfil de cliente.'
                );
        }

        // Consultar exclusivamente los pedidos del cliente.
        $pedidoModel = new PedidoModel();

        $pedidos = $pedidoModel
            ->where('cliente_id', $cliente['id'])
            ->orderBy('id', 'DESC')
            ->findAll();

        // Consultar los pagos asociados a esos pedidos.
        $pagoModel = new PagoPedidoModel();

        foreach ($pedidos as &$pedido) {
            $pedido['pagos'] = $pagoModel
                ->where('pedido_id', $pedido['id'])
                ->orderBy('id', 'DESC')
                ->findAll();

            // Sumar solamente los pagos verificados.
            $totalVerificadoCentavos = 0;

            foreach ($pedido['pagos'] as $pago) {
                if ($pago['estado'] !== 'VERIFICADO') {
                    continue;
                }

                [$quetzales, $centavos] = explode(
                    '.',
                    (string) $pago['monto']
                );

                $totalVerificadoCentavos +=
                    ((int) $quetzales * 100) +
                    (int) $centavos;
            }

            $pedido['total_verificado'] = number_format(
                $totalVerificadoCentavos / 100,
                2,
                '.',
                ''
            );
        }

        unset($pedido);

        // Mostrar la vista de pedidos.
        return view('cliente/mis_pedidos', [
            'pedidos' => $pedidos,
            'nombre'  => session('nombre'),
        ]);
    }
}
