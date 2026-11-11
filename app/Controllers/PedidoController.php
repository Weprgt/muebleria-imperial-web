<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\MuebleModel;
use App\Models\PedidoModel;
use App\Models\DetallePedidoModel;
use Throwable;

class PedidoController extends BaseController
{
    public function confirmar()
    {
        // 1. Verificar autenticacion y rol.
        if (
            session('logueado') !== true ||
            (int) session('rol_id') !== 4
        ) {
            return redirect()->to(site_url('login'))
                ->with('error', 'Debes ingresar como cliente.');
        }

        // 2. Identificar al cliente autenticado.
        $cliente = (new ClienteModel())
            ->where('usuario_id', session('usuario_id'))
            ->first();

        if (!$cliente) {
            return redirect()->to(site_url('carrito'))
                ->with('error', 'No se encontro tu perfil de cliente.');
        }

        // 3. Recuperar el carrito.
        $carrito = session()->get('carrito') ?? [];

        if (!is_array($carrito) || empty($carrito)) {
            return redirect()->to(site_url('carrito'))
                ->with('error', 'El carrito esta vacio.');
        }

        $db = db_connect();

        // 4. Iniciar una transaccion.
        $db->transBegin();

        try {
            $muebleModel = new MuebleModel();

            $detalles = [];
            $totalCentavos = 0;

            foreach ($carrito as $muebleId => $cantidad) {
                $id = filter_var(
                    $muebleId,
                    FILTER_VALIDATE_INT,
                    ['options' => ['min_range' => 1]]
                );

                $cantidadValidada = filter_var(
                    $cantidad,
                    FILTER_VALIDATE_INT,
                    ['options' => ['min_range' => 1]]
                );

                if (!$id || !$cantidadValidada) {
                    throw new \RuntimeException(
                        'El carrito contiene cantidades invalidas.'
                    );
                }

                // Consultar datos reales del producto.
                $mueble = $muebleModel
                    ->select('muebles.id, muebles.precio, muebles.stock, muebles.stock_reservado')
                    ->join(
                        'categorias_muebles',
                        'categorias_muebles.id = muebles.categoria_id'
                    )
                    ->where('muebles.id', $id)
                    ->where('muebles.activo', 1)
                    ->where('categorias_muebles.activo', 1)
                    ->first();

                if (!$mueble) {
                    throw new \RuntimeException(
                        'Uno de los muebles ya no esta disponible.'
                    );
                }

                $stockDisponible = max(
                    0,
                    (int) $mueble['stock'] - (int) $mueble['stock_reservado']
                );

                if ($cantidadValidada > $stockDisponible) {
                    throw new \RuntimeException(
                        'Las existencias cambiaron. Revisa tu carrito.'
                    );
                }

                // Calcular en centavos para evitar errores
                // de precision con numeros decimales.
                $precio = (string) $mueble['precio'];

                if (!preg_match('/^\d{1,10}\.\d{2}$/', $precio)) {
                    throw new \RuntimeException(
                        'El precio del producto no es valido.'
                    );
                }

                [$quetzales, $centavos] = explode('.', $precio);

                $precioCentavos =
                    ((int) $quetzales * 100) + (int) $centavos;

                if ($precioCentavos <= 0) {
                    throw new \RuntimeException(
                        'El precio del producto no es valido.'
                    );
                }

                $subtotalCentavos =
                    $precioCentavos * $cantidadValidada;

                $totalCentavos += $subtotalCentavos;

                if ($totalCentavos > 999999999999) {
                    throw new \RuntimeException(
                        'El importe del pedido supera el limite permitido.'
                    );
                }

                $detalles[] = [
                    'mueble_id'       => $id,
                    'cantidad'        => $cantidadValidada,
                    'precio_unitario' => number_format(
                        $precioCentavos / 100, 2, '.', ''
                    ),
                    'subtotal' => number_format(
                        $subtotalCentavos / 100, 2, '.', ''
                    ),
                ];
            }

            if (empty($detalles)) {
                throw new \RuntimeException(
                    'No hay productos validos en el carrito.'
                );
            }

            // 5. Calcular el anticipo inicial del 25 %.
            $anticipoCentavos = intdiv(
                $totalCentavos * 25 + 99,
                100
            );

            // 6. Registrar el pedido.
            $pedidoModel = new PedidoModel();

            $pedidoId = $pedidoModel->insert([
                'cliente_id'         => $cliente['id'],
                'canal'              => 'WEB',
                'estado'             => 'PENDIENTE_ANTICIPO',
                'total'              => number_format(
                    $totalCentavos / 100, 2, '.', ''
                ),
                'anticipo_requerido' => number_format(
                    $anticipoCentavos / 100, 2, '.', ''
                ),
            ]);

            if (!$pedidoId) {
                throw new \RuntimeException(
                    'No se pudo registrar el pedido.'
                );
            }

            // 7. Guardar cada detalle.
            $detalleModel = new DetallePedidoModel();

            foreach ($detalles as $detalle) {
                $detalle['pedido_id'] = $pedidoId;

                if (!$detalleModel->insert($detalle)) {
                    throw new \RuntimeException(
                        'No se pudo registrar el detalle del pedido.'
                    );
                }
            }

            if ($db->transStatus() === false) {
                throw new \RuntimeException(
                    'Ocurrio un error al guardar el pedido.'
                );
            }

            // 8. Confirmar la transaccion.
            $db->transCommit();

            // 9. Vaciar el carrito solo despues del exito.
            session()->remove('carrito');

            return redirect()->to(site_url('carrito'))
                ->with(
                    'success',
                    'Pedido #' . $pedidoId .
                    ' registrado. Pendiente de anticipo.'
                );

        } catch (Throwable $e) {
            $db->transRollback();

            log_message(
                'error',
                'Error al confirmar pedido: {mensaje}',
                ['mensaje' => $e->getMessage()]
            );

            return redirect()->to(site_url('carrito'))
                ->with('error', $e instanceof \RuntimeException
                    ? $e->getMessage()
                    : 'No se pudo registrar el pedido.');
        }
    }
}
