<?php

namespace App\Controllers;

use App\Models\CategoriaMuebleModel;

class AdminCategoriasController extends BaseController
{
    public function index()
    {
        // Crear una instancia del modelo.
        $categoriaModel = new CategoriaMuebleModel();

        // Consultar todas las categorías.
        $categorias = $categoriaModel
            ->orderBy('id', 'DESC')
            ->findAll();

        // Enviar las categorías a la vista.
        return view('admin/categorias/index', [
            'categorias' => $categorias
        ]);
    }
    public function agregar()
    {
        return view('admin/categorias/formulario');
    }
    public function guardar()
    {
        // 1. Definir las reglas de validación.
        $reglas = [
            'nombre'      => 'required|max_length[100]|is_unique[categorias_muebles.nombre]',
            'descripcion' => 'permit_empty',
        ];

        // 2. Validar los datos recibidos.
        if (! $this->validateData($this->request->getPost(), $reglas)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // 3. Obtener los datos validados.
        $datos = $this->validator->getValidated();

        // 4. Preparar el registro.
        $categoria = [
            'nombre'      => trim($datos['nombre']),
            'descripcion' => trim($datos['descripcion'] ?? ''),
            'activo'      => 1,
        ];

        // 5. Guardar en MySQL.
        $categoriaModel = new CategoriaMuebleModel();

        if (! $categoriaModel->insert($categoria)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', [
                    'general' => 'No se pudo guardar la categoría.'
                ]);
        }

        // 6. Regresar al listado.
        return redirect()->to(site_url('admin/categorias'))
            ->with('success', 'Categoría registrada correctamente.');
    }
    public function editar($id)
    {
        $categoriaModel = new CategoriaMuebleModel();

        // Buscar la categoría por su ID.
        $categoria = $categoriaModel->find($id);

        // Comprobar que exista.
        if (! $categoria) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                'La categoría solicitada no existe.'
            );
        }

        // Reutilizar el formulario existente.
        return view('admin/categorias/formulario', [
            'categoria' => $categoria
        ]);
    }
    public function actualizar($id)
    {
        $categoriaModel = new CategoriaMuebleModel();

        // 1. Comprobar que la categoría existe.
        $categoria = $categoriaModel->find($id);

        if (! $categoria) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                'La categoría solicitada no existe.'
            );
        }

        // 2. Definir las reglas de validación.
        $reglas = [
            'nombre' => "required|max_length[100]|is_unique[categorias_muebles.nombre,id,{$id}]",
            'descripcion' => 'permit_empty',
        ];

        // 3. Validar los datos recibidos.
        if (! $this->validateData($this->request->getPost(), $reglas)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // 4. Obtener los datos validados.
        $datos = $this->validator->getValidated();

        // 5. Preparar los datos actualizados.
        $datosActualizados = [
            'nombre' => trim($datos['nombre']),
            'descripcion' => trim($datos['descripcion'] ?? ''),
        ];

        // 6. Actualizar la categoría en MySQL.
        if (! $categoriaModel->update($id, $datosActualizados)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', [
                    'general' => 'No se pudo actualizar la categoría.'
                ]);
        }

        // 7. Regresar al listado.
        return redirect()->to(site_url('admin/categorias'))
            ->with('success', 'Categoría actualizada correctamente.');
    }
    public function desactivar($id)
    {
        $categoriaModel = new CategoriaMuebleModel();
        $muebleModel = new \App\Models\MuebleModel();

        // 1. Buscar la categoría.
        $categoria = $categoriaModel->find($id);

        if (! $categoria) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                'La categoría solicitada no existe.'
            );
        }

        // 2. Comprobar si tiene muebles activos.
        $mueblesActivos = $muebleModel
            ->where('categoria_id', $id)
            ->where('activo', 1)
            ->countAllResults();

        if ($mueblesActivos > 0) {
            return redirect()->to(site_url('admin/categorias'))
                ->with('error', 'No se puede desactivar una categoría que tiene muebles activos.');
        }

        // 3. Desactivar sin eliminar el registro.
        $categoriaModel->update($id, ['activo' => 0]);

        return redirect()->to(site_url('admin/categorias'))
            ->with('success', 'Categoría desactivada correctamente.');
    }
    public function reactivar($id)
    {
        $categoriaModel = new CategoriaMuebleModel();

        // 1. Buscar la categoría.
        $categoria = $categoriaModel->find($id);

        // 2. Comprobar que exista.
        if (! $categoria) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                'La categoría solicitada no existe.'
            );
        }

        // 3. Reactivar la categoría.
        if (! $categoriaModel->update($id, ['activo' => 1])) {
            return redirect()->to(site_url('admin/categorias'))
                ->with('error', 'No se pudo reactivar la categoría.');
        }

        // 4. Regresar al listado.
        return redirect()->to(site_url('admin/categorias'))
            ->with('success', 'Categoría reactivada correctamente.');
    }
}