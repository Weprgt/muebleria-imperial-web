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

        if ($rolId === 3) {
            $reparacionModel = new \App\Models\ReparacionModel();

            $datos['tallerPendientes'] = $reparacionModel
                ->whereIn('estado', ['SOLICITADA', 'EN_EVALUACION'])
                ->countAllResults();

            $datos['tallerEnReparacion'] = $reparacionModel
                ->where('estado', 'EN_REPARACION')
                ->countAllResults();

            $datos['tallerFinalizadas'] = $reparacionModel
                ->where('estado', 'FINALIZADA')
                ->countAllResults();
        }

        // Actividad reciente de MongoDB.
        // Exclusiva del administrador.
        if ($rolId === 1) {
            $auditoria = new \App\Services\AuditoriaMongoService();

            $paginaSolicitada = $this->request->getGet('auditoria_pagina');
            $paginaSolicitada = is_scalar($paginaSolicitada)
                ? filter_var(
                    (string) $paginaSolicitada,
                    FILTER_VALIDATE_INT,
                    ['options' => ['min_range' => 1]]
                )
                : false;

            if ($paginaSolicitada === false) {
                $paginaSolicitada = 1;
            }

            $resultadoAuditoria = $auditoria->obtenerRecientesPaginados(
                $paginaSolicitada,
                5
            );

            $datos['actividadReciente'] = $resultadoAuditoria['eventos'];
            $datos['auditoriaDisponible'] = $resultadoAuditoria['disponible'];
            $datos['auditoriaPagina'] = $resultadoAuditoria['pagina'];
            $datos['auditoriaTotalPaginas'] = $resultadoAuditoria['totalPaginas'];

            // Obtener los pedidos relacionados con los eventos de auditoría.
            $pedidosIds = [];

            foreach ($datos['actividadReciente'] as $evento) {
                $pedidoId = (int) ($evento['datos']['pedido_id'] ?? 0);

                if ($pedidoId > 0) {
                    $pedidosIds[] = $pedidoId;
                }
            }

            $pedidosIds = array_values(array_unique($pedidosIds));

            $datos['clientesAuditoria'] = [];

            if (!empty($pedidosIds)) {
                $clientes = \Config\Database::connect()
                    ->table('pedidos')
                    ->select('pedidos.id AS pedido_id, usuarios.nombre, usuarios.apellido')
                    ->join('clientes', 'clientes.id = pedidos.cliente_id')
                    ->join('usuarios', 'usuarios.id = clientes.usuario_id')
                    ->whereIn('pedidos.id', $pedidosIds)
                    ->get()
                    ->getResultArray();

                foreach ($clientes as $cliente) {
                    $datos['clientesAuditoria'][(int) $cliente['pedido_id']] =
                        trim($cliente['nombre'] . ' ' . $cliente['apellido']);
                }
            }
            // Obtener los muebles asociados a los pedidos auditados.
            $datos['mueblesAuditoria'] = [];

            if (!empty($pedidosIds)) {
                $muebles = \Config\Database::connect()
                    ->table('detalle_pedido')
                    ->select('detalle_pedido.pedido_id, detalle_pedido.cantidad, muebles.nombre')
                    ->join('muebles', 'muebles.id = detalle_pedido.mueble_id')
                    ->whereIn('detalle_pedido.pedido_id', $pedidosIds)
                    ->get()
                    ->getResultArray();

                foreach ($muebles as $mueble) {
                    $pedidoId = (int) $mueble['pedido_id'];

                    $datos['mueblesAuditoria'][$pedidoId][] =
                        $mueble['nombre'] . ' (x' . $mueble['cantidad'] . ')';
                }
            }
        }

        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, private')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', '0')
            ->setBody(view('dashboard/index', $datos));
    }
}
