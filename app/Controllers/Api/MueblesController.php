<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\MuebleModel;
use CodeIgniter\HTTP\ResponseInterface;

class MueblesController extends BaseController
{
    public function index(): ResponseInterface
    {
        $buscar = trim((string) $this->request->getGet('buscar'));
        $orden = $this->request->getGet('orden') ?? 'nombre_asc';

        $pagina = $this->request->getGet('pagina') ?? '1';
        $limite = $this->request->getGet('limite') ?? '10';

        $pagina = filter_var(
            $pagina,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        $limite = filter_var(
            $limite,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1, 'max_range' => 50]]
        );

        if ($pagina === false || $limite === false) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'success' => false,
                    'message' => 'La pagina debe ser positiva y el limite debe estar entre 1 y 50.',
                ]);
        }

        $ordenesPermitidos = [
            'nombre_asc'  => ['muebles.nombre', 'ASC'],
            'nombre_desc' => ['muebles.nombre', 'DESC'],
            'precio_asc'  => ['muebles.precio', 'ASC'],
            'precio_desc' => ['muebles.precio', 'DESC'],
        ];

        if (! is_string($orden) || ! isset($ordenesPermitidos[$orden])) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'success' => false,
                    'message' => 'El criterio de ordenamiento no es válido.',
                ]);
        }
        $categoriaId = $this->request->getGet('categoria_id');

        if ($categoriaId !== null) {
            $categoriaId = filter_var(
                $categoriaId,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            );

            if ($categoriaId === false) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'success' => false,
                        'message' => 'El ID de categoría debe ser un entero positivo.',
                    ]);
            }
        }
        $precioMin = $this->request->getGet('precio_min');
        $precioMax = $this->request->getGet('precio_max');

        foreach (['precio_min' => &$precioMin, 'precio_max' => &$precioMax] as $nombre => &$valor) {
            if ($valor === null) {
                continue;
            }

            if (! is_scalar($valor) || ! is_numeric($valor) || ! is_finite((float) $valor) || (float) $valor < 0) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'success' => false,
                        'message' => "El parámetro {$nombre} debe ser un número válido mayor o igual a cero.",
                    ]);
            }

            $valor = (float) $valor;
        }
        unset($valor);
        $modelo = new MuebleModel();

        $consulta = $modelo
            ->select('
                muebles.id,
                muebles.categoria_id,
                muebles.nombre,
                muebles.descripcion,
                muebles.precio,
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
        if ($categoriaId !== null) {
            $consulta->where('muebles.categoria_id', $categoriaId);
        }
        if ($precioMin !== null) {
            $consulta->where('muebles.precio >=', $precioMin);
        }

        if ($precioMax !== null) {
            $consulta->where('muebles.precio <=', $precioMax);
        }

        if ($precioMin !== null && $precioMax !== null && $precioMin > $precioMax) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'success' => false,
                    'message' => 'El precio mínimo no puede ser mayor que el máximo.',
                ]);
        }
        
        [$columnaOrden, $direccionOrden] = $ordenesPermitidos[$orden];

        // Contar los muebles que cumplen los filtros.
        $total = $consulta->countAllResults(false);

        // Calcular el número total de páginas.
        $totalPaginas = (int) ceil($total / $limite);

        // Calcular desde qué registro comenzar.
        $offset = ($pagina - 1) * $limite;

        // Obtener únicamente los muebles de la página solicitada.
        $muebles = $consulta
            ->orderBy($columnaOrden, $direccionOrden)
            ->orderBy('muebles.id', 'ASC')
            ->findAll($limite, $offset);


        foreach ($muebles as &$mueble) {
            $mueble['id'] = (int) $mueble['id'];
            $mueble['categoria_id'] = (int) $mueble['categoria_id'];
            $mueble['precio'] = (float) $mueble['precio'];
            $mueble['stock_disponible'] =
                (int) $mueble['stock_disponible'];
        }

        unset($mueble);
        
        return $this->response->setJSON([
            'success'       => true,
            'total'         => $total,
            'pagina'        => $pagina,
            'limite'        => $limite,
            'total_paginas' => $totalPaginas,
            'data'          => $muebles,
        ]);
    }

    public function show(int $id): ResponseInterface
    {
        $modelo = new MuebleModel();

        $mueble = $modelo
            ->select('
                muebles.id,
                muebles.categoria_id,
                muebles.nombre,
                muebles.descripcion,
                muebles.precio,
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
            ->where('categorias_muebles.activo', 1)
            ->where('muebles.id', $id)
            ->first();

        if ($mueble === null) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' => 'Mueble no encontrado.',
                ]);
        }

        $mueble['id'] = (int) $mueble['id'];
        $mueble['categoria_id'] = (int) $mueble['categoria_id'];
        $mueble['precio'] = (float) $mueble['precio'];
        $mueble['stock_disponible'] =
            (int) $mueble['stock_disponible'];

        return $this->response->setJSON([
            'success' => true,
            'data'    => $mueble,
        ]);
    }
}
