<?php

namespace App\Controllers;

use App\Models\MuebleModel;

class CarritoController extends BaseController
{
    
    public function index()
    {
        $carrito = session()->get('carrito') ?? [];

        $productos = [];
        $total = 0;

        $muebleModel = new MuebleModel();

        foreach ($carrito as $muebleId => $cantidad) {

            $mueble = $muebleModel
                ->select('
                    muebles.id,
                    muebles.nombre,
                    muebles.precio,
                    muebles.stock,
                    muebles.imagen_principal
                ')
                ->join(
                    'categorias_muebles',
                    'categorias_muebles.id = muebles.categoria_id'
                )
                ->where('muebles.id', $muebleId)
                ->where('muebles.activo', 1)
                ->where('categorias_muebles.activo', 1)
                ->first();

            if (!$mueble) {
                continue;
            }

            $cantidad = max(1, (int) $cantidad);
            $cantidad = min($cantidad, (int) $mueble['stock']);

            if ($cantidad <= 0) {
                continue;
            }

            $subtotal = (float) $mueble['precio'] * $cantidad;

            $productos[] = [
                'id'               => $mueble['id'],
                'nombre'           => $mueble['nombre'],
                'precio'           => $mueble['precio'],
                'cantidad'         => $cantidad,
                'stock'            => $mueble['stock'],
                'imagen_principal' => $mueble['imagen_principal'],
                'subtotal'         => $subtotal,
            ];

            $total += $subtotal;
        }

        return view('carrito/index', [
            'productos' => $productos,
            'total'     => $total,
        ]);
    }

    public function agregar()
    {
        $muebleId = filter_var(
            $this->request->getPost('mueble_id'),
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        if ($muebleId === false || $muebleId === null) {
            return redirect()->to(site_url('catalogo'))
                ->with('error', 'El mueble seleccionado no es válido.');
        }

        $muebleModel = new MuebleModel();

        $mueble = $muebleModel
            ->select('muebles.id, muebles.stock')
            ->join(
                'categorias_muebles',
                'categorias_muebles.id = muebles.categoria_id'
            )
            ->where('muebles.id', $muebleId)
            ->where('muebles.activo', 1)
            ->where('categorias_muebles.activo', 1)
            ->first();

        if (!$mueble || (int) $mueble['stock'] <= 0) {
            return redirect()->to(site_url('catalogo'))
                ->with('error', 'Este mueble no está disponible.');
        }

        $session = session();
        $carrito = $session->get('carrito') ?? [];

        $cantidadActual = (int) ($carrito[$muebleId] ?? 0);

        if ($cantidadActual >= (int) $mueble['stock']) {
            return redirect()->to(site_url('catalogo'))
                ->with('error', 'No hay suficientes existencias.');
        }

        $carrito[$muebleId] = $cantidadActual + 1;

        $session->set('carrito', $carrito);

        return redirect()->to(site_url('catalogo'))
            ->with('success', 'Mueble agregado al carrito correctamente.');
    }


    public function eliminar()
    {
        $muebleId = filter_var(
            $this->request->getPost('mueble_id'),
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        if ($muebleId === false || $muebleId === null) {
            return redirect()->to(site_url('carrito'))
                ->with('error', 'El producto seleccionado no es válido.');
        }

        $carrito = session()->get('carrito') ?? [];

        if (isset($carrito[$muebleId])) {
            unset($carrito[$muebleId]);

            session()->set('carrito', $carrito);

            return redirect()->to(site_url('carrito'))
                ->with('success', 'Producto eliminado del carrito.');
        }

        return redirect()->to(site_url('carrito'))
            ->with('error', 'El producto no se encuentra en el carrito.');
    }

        public function actualizar()
        {
            $muebleId = filter_var(
                $this->request->getPost('mueble_id'),
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            );

            $cantidad = filter_var(
                $this->request->getPost('cantidad'),
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            );

            if (!$muebleId || !$cantidad) {
                return redirect()->to(site_url('carrito'))
                    ->with('error', 'La cantidad indicada no es válida.');
            }

            $carrito = session()->get('carrito') ?? [];

            if (!isset($carrito[$muebleId])) {
                return redirect()->to(site_url('carrito'))
                    ->with('error', 'El producto no está en el carrito.');
            }

            $muebleModel = new MuebleModel();

            $mueble = $muebleModel
                ->select('muebles.id, muebles.stock')
                ->join(
                    'categorias_muebles',
                    'categorias_muebles.id = muebles.categoria_id'
                )
                ->where('muebles.id', $muebleId)
                ->where('muebles.activo', 1)
                ->where('categorias_muebles.activo', 1)
                ->first();

            if (!$mueble || (int) $mueble['stock'] <= 0) {
                return redirect()->to(site_url('carrito'))
                    ->with('error', 'Este producto ya no está disponible.');
            }

            if ($cantidad > (int) $mueble['stock']) {
                return redirect()->to(site_url('carrito'))
                    ->with('error', 'La cantidad supera las existencias disponibles.');
            }

            $carrito[$muebleId] = $cantidad;

            session()->set('carrito', $carrito);

            return redirect()->to(site_url('carrito'))
                ->with('success', 'Cantidad actualizada correctamente.');
        }


}
