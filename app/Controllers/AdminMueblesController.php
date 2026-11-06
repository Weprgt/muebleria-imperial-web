<?php

namespace App\Controllers;

use App\Models\MuebleModel;

class AdminMueblesController extends BaseController
{
    public function index()
    {
        $muebleModel = new MuebleModel();

        $muebles = $muebleModel
            ->select('muebles.*, categorias_muebles.nombre AS categoria')
            ->join(
                'categorias_muebles',
                'categorias_muebles.id = muebles.categoria_id',
                'left'
            )
            ->orderBy('muebles.id', 'DESC')
            ->findAll();

        return view('admin/muebles/index', [
            'muebles' => $muebles
        ]);
    }

    public function agregar()
    {
        $categoriaModel = new \App\Models\CategoriaMuebleModel();

        $categorias = $categoriaModel
            ->where('activo', 1)
            ->orderBy('nombre', 'ASC')
            ->findAll();

        return view('admin/muebles/formulario', [
            'categorias' => $categorias
        ]);
    }

    
    public function guardar()
    {
        // 1. Validar los datos recibidos del formulario.
        $reglas = [
            'categoria_id' => 'required|is_natural_no_zero',
            'nombre'       => 'required|max_length[150]',
            'descripcion'  => 'permit_empty',
            'precio'       => 'required|numeric|greater_than[0]',
            'stock'        => 'required|is_natural',
            'imagen_principal' => 'permit_empty|max_length[255]',
        ];

        if (! $this->validateData($this->request->getPost(), $reglas)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // 2. Obtener los datos validados.
        $datos = $this->validator->getValidated();

        // 3. Verificar que la categoría exista y esté activa.
        $categoriaModel = new \App\Models\CategoriaMuebleModel();

        $categoria = $categoriaModel
            ->where('id', $datos['categoria_id'])
            ->where('activo', 1)
            ->first();

        if (! $categoria) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', [
                    'categoria_id' => 'La categoría seleccionada no es válida.'
                ]);
        }

        // 4. Preparar los datos para MySQL.
        $mueble = [
            'categoria_id'     => $datos['categoria_id'],
            'nombre'           => trim($datos['nombre']),
            'descripcion'      => trim($datos['descripcion'] ?? ''),
            'precio'           => $datos['precio'],
            'stock'            => $datos['stock'],
            'imagen_principal' => trim($datos['imagen_principal'] ?? ''),
            'activo'           => 1,
        ];

        // 5. Guardar el mueble.
        $muebleModel = new MuebleModel();

        if (! $muebleModel->insert($mueble)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', [
                    'general' => 'No se pudo guardar el mueble.'
                ]);
        }

        // 6. Regresar al listado.
        return redirect()
            ->to(site_url('admin/muebles'))
            ->with('success', 'Mueble registrado correctamente.');
    }


}
