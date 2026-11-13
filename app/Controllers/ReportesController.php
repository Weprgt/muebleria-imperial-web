<?php

namespace App\Controllers;

class ReportesController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // Pedidos agrupados por estado.
        $datos['pedidosPorEstado'] = $db->table('pedidos')
            ->select('estado, COUNT(*) AS cantidad, SUM(total) AS monto')
            ->groupBy('estado')
            ->orderBy('estado', 'ASC')
            ->get()
            ->getResultArray();

        // Inventario de muebles activos.
        $datos['reporteInventario'] = $db->table('muebles')
            ->select('nombre, stock, stock_reservado')
            ->where('activo', 1)
            ->orderBy('nombre', 'ASC')
            ->get()
            ->getResultArray();

        // Reparaciones agrupadas por estado.
        $datos['reparacionesPorEstado'] = $db->table('reparaciones')
            ->select('estado, COUNT(*) AS cantidad')
            ->groupBy('estado')
            ->orderBy('estado', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, private')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', '0')
            ->setBody(view('reportes/index', $datos));
    }
}
