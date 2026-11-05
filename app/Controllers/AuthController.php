<?php

namespace App\Controllers;

use App\Libraries\LoginAttemptLimiter;
use App\Models\UsuarioModel;

class AuthController extends BaseController
{
    public function login()
    {
        return view('auth/login');
    }

    public function autenticar()
    {
        $reglas = [
            'correo'   => 'required|valid_email',
            'password' => 'required',
        ];

        if (! $this->validate($reglas)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $correo = strtolower(trim(
            (string) $this->request->getPost('correo')
        ));

        $password = (string) $this->request->getPost('password');

        $usuarioModel = new UsuarioModel();

        $usuario = $usuarioModel
            ->where('correo', $correo)
            ->first();

        $limitador = new LoginAttemptLimiter();

        $resultado = $limitador->ejecutar(
            $correo,
            $this->request->getIPAddress(),
            function () use ($usuario, $password): bool {
                return $usuario !== null
                    && password_verify(
                        $password,
                        $usuario['password_hash']
                    )
                    && (int) $usuario['activo'] === 1;
            }
        );

        if ($resultado['estado'] === 'bloqueado') {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Demasiados intentos. Inténtalo nuevamente más tarde.'
                );
        }

        if ($resultado['estado'] === 'incorrecto') {
            return redirect()
                ->back()
                ->with('error', 'Credenciales incorrectas.');
        }

        session()->regenerate(true);

        session()->set([
            'usuario_id' => $usuario['id'],
            'rol_id'     => $usuario['rol_id'],
            'nombre'     => $usuario['nombre'],
            'logueado'   => true,
        ]);

        return redirect()->to('/dashboard');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('/login');
    }
}