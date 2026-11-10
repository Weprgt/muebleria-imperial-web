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
            'imagen_principal' => 'permit_empty|is_image[imagen_principal]|mime_in[imagen_principal,image/jpg,image/jpeg,image/png,image/webp]|max_size[imagen_principal,2048]',
        ];

        $datosFormulario = array_merge(
            $this->request->getPost(),
            $this->request->getFiles()
        );

        if (! $this->validateData($datosFormulario, $reglas)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // 2. Obtener los datos validados.
        $datos = $this->validator->getValidated();
        $imagen = $this->request->getFile('imagen_principal');
        $rutaImagen = '';

        if ($imagen !== null && $imagen->isValid()) {

            // Generar un nombre único para evitar duplicados.
            $nombreImagen = $imagen->getRandomName();

            // Guardar la fotografía en la carpeta pública.
            try {
                $imagen->move(
                    FCPATH . 'uploads/muebles',
                    $nombreImagen
                );
            } catch (\Throwable $e) {
                log_message('error', 'Error al subir imagen: ' . $e->getMessage());

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('errors', [
                        'imagen_principal' => 'No se pudo guardar la fotografía.'
                    ]);
            }

            // Preparar la ruta que almacenaremos en MySQL.
            $rutaImagen = 'uploads/muebles/' . $nombreImagen;
        }

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
            'imagen_principal' => $rutaImagen,
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

    public function editar($id)
    {
        $muebleModel = new MuebleModel();

        // Buscar el mueble por su ID.
        $mueble = $muebleModel->find($id);

        // Verificar que el mueble exista.
        if (! $mueble) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                'El mueble solicitado no existe.'
            );
        }

        // Obtener las categorías activas.
        $categoriaModel = new \App\Models\CategoriaMuebleModel();

        $categorias = $categoriaModel
            ->where('activo', 1)
            ->orderBy('nombre', 'ASC')
            ->findAll();

        // Enviar los datos a la vista de edición.
        return view('admin/muebles/formulario', [
            'mueble'     => $mueble,
            'categorias' => $categorias
        ]);
    }

    public function actualizar($id)
    {
        $muebleModel = new MuebleModel();

        // Comprobar que el mueble existe.
        $mueble = $muebleModel->find($id);

        if (! $mueble) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                'El mueble solicitado no existe.'
            );
        }

        // Validar los datos recibidos.
        $reglas = [
            'categoria_id' => 'required|is_natural_no_zero',
            'nombre' => 'required|max_length[150]',
            'descripcion' => 'permit_empty',
            'precio' => 'required|numeric|greater_than[0]',
            'stock' => 'required|is_natural',
            'imagen_principal' => 'permit_empty|is_image[imagen_principal]|mime_in[imagen_principal,image/jpg,image/jpeg,image/png,image/webp]|max_size[imagen_principal,2048]',
        ];

        $datosFormulario = array_merge(
            $this->request->getPost(),
            $this->request->getFiles()
        );

        if (! $this->validateData($datosFormulario, $reglas)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $datos = $this->validator->getValidated();
        $imagen = $this->request->getFile('imagen_principal');
        $rutaImagen = $mueble['imagen_principal'] ?? '';
        if ($imagen !== null && $imagen->isValid()) {

            // Generar un nombre único para la nueva fotografía.
            $nombreImagen = $imagen->getRandomName();

            try {
                // Guardar la nueva fotografía.
                $imagen->move(
                    FCPATH . 'uploads/muebles',
                    $nombreImagen
                );

                // Actualizar la ruta de la fotografía.
                $rutaImagen = 'uploads/muebles/' . $nombreImagen;

            } catch (\Throwable $e) {

                log_message(
                    'error',
                    'Error al actualizar imagen: ' . $e->getMessage()
                );

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('errors', [
                        'imagen_principal' => 'No se pudo guardar la nueva fotografía.'
                    ]);
            }
        }

        // Comprobar que la categoría existe y está activa.
        $categoriaModel = new \App\Models\CategoriaMuebleModel();

        $categoria = $categoriaModel
            ->where('id', $datos['categoria_id'])
            ->where('activo', 1)
            ->first();

        if (! $categoria) {
            return redirect()->back()
                ->withInput()
                ->with('errors', [
                    'categoria_id' => 'La categoría seleccionada no es válida.'
                ]);
        }

        // Preparar los datos actualizados.
        $datosActualizados = [
            'categoria_id' => $datos['categoria_id'],
            'nombre' => trim($datos['nombre']),
            'descripcion' => trim($datos['descripcion'] ?? ''),
            'precio' => $datos['precio'],
            'stock' => $datos['stock'],
            'imagen_principal' => $rutaImagen,
        ];

        // Actualizar el registro en MySQL.
        if (! $muebleModel->update($id, $datosActualizados)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', [
                    'general' => 'No se pudo actualizar el mueble.'
                ]);
        }

        return redirect()->to(site_url('admin/muebles'))
            ->with('success', 'Mueble actualizado correctamente.');
    }

    public function cambiarEstado($id)
    {
        $muebleModel = new MuebleModel();

        // Buscar el mueble por su ID.
        $mueble = $muebleModel->find($id);

        // Comprobar que existe.
        if (! $mueble) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                'El mueble solicitado no existe.'
            );
        }

        // Cambiar el estado actual.
        $nuevoEstado = (int) $mueble['activo'] === 1 ? 0 : 1;

        // Actualizar únicamente el campo activo.
        if (! $muebleModel->update($id, ['activo' => $nuevoEstado])) {
            return redirect()->to(site_url('admin/muebles'))
                ->with('errors', [
                    'general' => 'No se pudo cambiar el estado del mueble.'
                ]);
        }

        $mensaje = $nuevoEstado === 1
            ? 'Mueble activado correctamente.'
            : 'Mueble desactivado correctamente.';

        return redirect()->to(site_url('admin/muebles'))
            ->with('success', $mensaje);
    }

}
