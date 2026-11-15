<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\CategoriaMuebleModel;
use CodeIgniter\HTTP\ResponseInterface;

class CategoriasController extends BaseController
{
    public function index(): ResponseInterface
    {
        $modelo = new CategoriaMuebleModel();

        $categorias = $modelo
            ->select('id, nombre, descripcion')
            ->where('activo', 1)
            ->orderBy('nombre', 'ASC')
            ->findAll();

        foreach ($categorias as &$categoria) {
            $categoria['id'] = (int) $categoria['id'];
        }

        unset($categoria);

        return $this->response->setJSON([
            'success' => true,
            'total'   => count($categorias),
            'data'    => $categorias,
        ]);
    }
}
