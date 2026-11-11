<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;
use App\Models\ClienteModel;

class RegistroController extends BaseController
{
    public function index()
    {
        return view('auth/registro');
    }

    public function guardar()
    {
        $reglas = [
            'nombre' => 'required|min_length[2]|max_length[100]',
            'apellido' => 'required|min_length[2]|max_length[100]',
            'correo' => 'required|valid_email|max_length[150]|is_unique[usuarios.correo]',
            'password' => 'required|min_length[8]|max_length[72]',
            'confirmar_password' => 'required|matches[password]',
            'telefono' => 'permit_empty|max_length[20]',
            'direccion' => 'permit_empty|max_length[1000]',
        ];

        if (! $this->validate($reglas)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $usuarioModel = new UsuarioModel();
        $clienteModel = new ClienteModel();
        $db = db_connect();

        $db->transBegin();

        try {
            $usuarioId = $usuarioModel->insert([
                'rol_id' => 4,
                'nombre' => trim($this->request->getPost('nombre')),
                'apellido' => trim($this->request->getPost('apellido')),
                'correo' => strtolower(trim($this->request->getPost('correo'))),
                'password_hash' => password_hash(
                    $this->request->getPost('password'),
                    PASSWORD_BCRYPT
                ),
                'activo' => 1,
            ]);

            if ($usuarioId === false) {
                throw new \RuntimeException('No se pudo crear el usuario.');
            }

            $clienteId = $clienteModel->insert([
                'usuario_id' => $usuarioId,
                'telefono' => $this->request->getPost('telefono') ?: null,
                'direccion' => $this->request->getPost('direccion') ?: null,
            ]);

            if ($clienteId === false || $db->transStatus() === false) {
                throw new \RuntimeException('No se pudo crear el perfil del cliente.');
            }

            $db->transCommit();

            return redirect()->to('/login')
                ->with('success', 'Cuenta creada correctamente. Ya puedes iniciar sesión.');

        } catch (\Throwable $e) {
            $db->transRollback();

            log_message('error', 'Error al registrar cliente: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'No se pudo completar el registro. Verifica tus datos e inténtalo nuevamente.');
        }
    }
}
