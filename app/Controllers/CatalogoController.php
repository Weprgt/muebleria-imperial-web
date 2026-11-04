<?php

namespace App\Controllers;

use App\Models\MuebleModel;

class CatalogoController extends BaseController
{
    public function index()
    {
        $muebleModel = new MuebleModel();

        $muebles = $muebleModel
            ->select('
                muebles.id,
                muebles.nombre,
                muebles.descripcion,
                muebles.precio,
                muebles.stock,
                muebles.imagen_principal,
                categorias_muebles.nombre AS categoria
            ')
            ->join(
                'categorias_muebles',
                'categorias_muebles.id = muebles.categoria_id'
            )
            ->where('muebles.activo', 1)
            ->where('categorias_muebles.activo', 1)
            ->orderBy('muebles.nombre', 'ASC')
            ->findAll();

        return view('frontend/catalogo', [
            'muebles' => $muebles
        ]);
    }
}
