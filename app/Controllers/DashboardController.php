<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    public function index()
    {
        $datos = [];

        $rolId = (int) session()->get('rol_id');

        if (in_array($rolId, [1, 2], true)) {
            $db = \Config\Database::connect();


            // Total de pedidos registrados.
            $datos['totalPedidos'] = $db->table('pedidos')
                ->countAllResults();

            // Pedidos entregados.
            $datos['pedidosEntregados'] = $db->table('pedidos')
                ->where('estado', 'ENTREGADO')
                ->countAllResults();

            // Importe de pedidos entregados.
            // No incluye pedidos cancelados ni pendientes.
            $ventas = $db->table('pedidos')
                ->selectSum('total')
                ->where('estado', 'ENTREGADO')
                ->get()
                ->getRow();

            $datos['ventasEntregadas'] =
                (float) ($ventas->total ?? 0);

            // Pagos pendientes de verificación.
            $datos['pagosPendientes'] = $db->table('pagos_pedido')
                ->where('estado', 'PENDIENTE')
                ->countAllResults();

            // Productos activos.
            $datos['productosActivos'] = $db->table('muebles')
                ->where('activo', 1)
                ->countAllResults();

            // Existencias y reservas.
            $inventario = $db->table('muebles')
                ->selectSum('stock')
                ->selectSum('stock_reservado')
                ->where('activo', 1)
                ->get()
                ->getRow();

            $datos['unidadesDisponibles'] =
                (int) ($inventario->stock ?? 0)
                - (int) ($inventario->stock_reservado ?? 0);

            // Reparaciones todavía no entregadas.
            $datos['reparacionesActivas'] = $db->table('reparaciones')
                ->whereNotIn('estado', ['ENTREGADA', 'CANCELADA', 'RECHAZADA'])
                ->countAllResults();
        }

        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, private')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', '0')
            ->setBody(view('dashboard/index', $datos));
    }
}
