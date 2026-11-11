<?php

namespace App\Controllers;

use App\Models\MuebleModel;

class CatalogoController extends BaseController
{
    public function index()
    {
        $muebleModel = new MuebleModel();

        $buscar = trim((string) $this->request->getGet('buscar'));

        $consulta = $muebleModel
            ->select('
                muebles.id,
                muebles.nombre,
                muebles.descripcion,
                muebles.precio,
                muebles.stock,
                muebles.stock_reservado,
                GREATEST(
                    muebles.stock - muebles.stock_reservado,
                    0
                ) AS stock_disponible,
                muebles.imagen_principal,
                categorias_muebles.nombre AS categoria
            ', false)
            ->join(
                'categorias_muebles',
                'categorias_muebles.id = muebles.categoria_id'
            )
            ->where('muebles.activo', 1)
            ->where('categorias_muebles.activo', 1);

        if ($buscar !== '') {
            $consulta->like('muebles.nombre', $buscar);
        }

        $muebles = $consulta
            ->orderBy('muebles.nombre', 'ASC')
            ->findAll();

        // Mantener compatibilidad con la vista actual.
        // La propiedad stock representará unidades disponibles.
        foreach ($muebles as &$mueble) {
            $mueble['stock'] = (int) $mueble['stock_disponible'];
        }

        unset($mueble);

        return view('frontend/catalogo', [
            'muebles' => $muebles,
            'buscar'  => $buscar,
        ]);
    }
}
