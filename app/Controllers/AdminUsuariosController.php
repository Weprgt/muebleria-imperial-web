<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class AdminUsuariosController extends BaseController
{
    public function index()
    {
        $usuarios = (new UsuarioModel())
            ->select('id, rol_id, nombre, apellido, correo, activo')
            ->whereIn('rol_id', [2, 3])
            ->orderBy('rol_id', 'ASC')
            ->orderBy('apellido', 'ASC')
            ->orderBy('nombre', 'ASC')
            ->findAll();

        return view('admin/usuarios/index', [
            'usuarios' => $usuarios,
        ]);
    }

    public function crear()
    {
        return view('admin/usuarios/formulario');
    }

    public function guardar()
    {
        $entrada = $this->obtenerEntradaFormulario();

        $reglas = [
            'nombre' => 'required|min_length[2]|max_length[100]',
            'apellido' => 'required|min_length[2]|max_length[100]',
            'correo' => 'required|valid_email|max_length[150]|is_unique[usuarios.correo]',
            'password' => 'required|min_length[8]|max_length[72]',
            'confirmar_password' => 'required|matches[password]',
            'rol_id' => 'required|in_list[2,3]',
        ];

        $mensajes = [
            'nombre' => [
                'required' => 'El nombre es obligatorio.',
                'min_length' => 'El nombre debe tener al menos 2 caracteres.',
                'max_length' => 'El nombre no puede superar 100 caracteres.',
            ],
            'apellido' => [
                'required' => 'El apellido es obligatorio.',
                'min_length' => 'El apellido debe tener al menos 2 caracteres.',
                'max_length' => 'El apellido no puede superar 100 caracteres.',
            ],
            'correo' => [
                'required' => 'El correo es obligatorio.',
                'valid_email' => 'Ingresa un correo válido.',
                'max_length' => 'El correo no puede superar 150 caracteres.',
                'is_unique' => 'Este correo ya está registrado.',
            ],
            'password' => [
                'required' => 'La contraseña es obligatoria.',
                'min_length' => 'La contraseña debe tener al menos 8 caracteres.',
                'max_length' => 'La contraseña no puede superar 72 caracteres.',
            ],
            'confirmar_password' => [
                'required' => 'Confirma la contraseña.',
                'matches' => 'Las contraseñas no coinciden.',
            ],
            'rol_id' => [
                'required' => 'Selecciona un rol.',
                'in_list' => 'El rol seleccionado no es válido.',
            ],
        ];

        if (! $this->validateData($entrada, $reglas, $mensajes)) {
            return $this->volverAlFormulario(
                $this->validator->getErrors(),
                $entrada
            );
        }

        $datosValidados = $this->validator->getValidated();
        $rolId = filter_var($datosValidados['rol_id'], FILTER_VALIDATE_INT);

        if (! in_array($rolId, [2, 3], true)) {
            return $this->volverAlFormulario(
                ['rol_id' => 'El rol seleccionado no es válido.'],
                $entrada
            );
        }

        if (strlen($datosValidados['password']) > 72) {
            return $this->volverAlFormulario(
                ['password' => 'La contraseña no puede superar 72 bytes.'],
                $entrada
            );
        }

        $passwordHash = password_hash(
            $datosValidados['password'],
            PASSWORD_DEFAULT
        );

        if ($passwordHash === false) {
            log_message('error', 'No se pudo generar el hash de una contraseña de empleado.');

            return $this->volverAlFormulario(
                ['general' => 'No se pudo crear el empleado. Inténtalo nuevamente.'],
                $entrada
            );
        }

        $usuarioModel = new UsuarioModel();
        $empleado = [
            'rol_id' => $rolId,
            'nombre' => trim($datosValidados['nombre']),
            'apellido' => trim($datosValidados['apellido']),
            'correo' => strtolower(trim($datosValidados['correo'])),
            'password_hash' => $passwordHash,
            'activo' => 1,
        ];

        try {
            $insertado = $usuarioModel->insert($empleado);
        } catch (DatabaseException $exception) {
            if ($this->esDuplicidadCorreo($exception)) {
                return $this->volverAlFormulario(
                    ['correo' => 'Este correo ya está registrado.'],
                    $entrada
                );
            }

            log_message(
                'error',
                'Error de base de datos al crear empleado. Código: '
                . $exception->getCode()
            );

            return $this->volverAlFormulario(
                ['general' => 'No se pudo crear el empleado. Inténtalo nuevamente.'],
                $entrada
            );
        }

        if ($insertado === false) {
            $erroresModelo = $usuarioModel->errors();

            if ($erroresModelo !== []) {
                return $this->volverAlFormulario($erroresModelo, $entrada);
            }

            $errorBaseDatos = db_connect()->error();

            if ((int) ($errorBaseDatos['code'] ?? 0) === 1062) {
                return $this->volverAlFormulario(
                    ['correo' => 'Este correo ya está registrado.'],
                    $entrada
                );
            }

            log_message(
                'error',
                'No se pudo crear empleado en la base de datos. Código: '
                . (int) ($errorBaseDatos['code'] ?? 0)
            );

            return $this->volverAlFormulario(
                ['general' => 'No se pudo crear el empleado. Inténtalo nuevamente.'],
                $entrada
            );
        }

        return redirect()
            ->to(site_url('admin/usuarios'))
            ->with('success', 'Empleado creado correctamente.');
    }

    public function cambiarEstado(int $id)
    {
        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel
            ->select('id, rol_id, activo')
            ->find($id);

        if ($usuario === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                'La cuenta solicitada no existe.'
            );
        }

        if (! in_array((int) $usuario['rol_id'], [2, 3], true)) {
            return redirect()
                ->to(site_url('admin/usuarios'))
                ->with('error', 'Solo se pueden cambiar cuentas de Ventas o Taller.');
        }

        $activoActual = (int) $usuario['activo'];

        if (! in_array($activoActual, [0, 1], true)) {
            return redirect()
                ->to(site_url('admin/usuarios'))
                ->with('error', 'No se pudo cambiar el estado de la cuenta.');
        }

        try {
            $actualizado = $usuarioModel->update($id, [
                'activo' => $activoActual === 1 ? 0 : 1,
            ]);
        } catch (DatabaseException $exception) {
            log_message(
                'error',
                'Error de base de datos al cambiar estado de empleado. Código: '
                . $exception->getCode()
            );

            return redirect()
                ->to(site_url('admin/usuarios'))
                ->with('error', 'No se pudo cambiar el estado de la cuenta.');
        }

        if (! $actualizado) {
            return redirect()
                ->to(site_url('admin/usuarios'))
                ->with('error', 'No se pudo cambiar el estado de la cuenta.');
        }

        return redirect()
            ->to(site_url('admin/usuarios'))
            ->with(
                'success',
                $activoActual === 1
                    ? 'Cuenta desactivada correctamente.'
                    : 'Cuenta activada correctamente.'
            );
    }

    private function obtenerEntradaFormulario(): array
    {
        $entrada = [];

        foreach ([
            'nombre',
            'apellido',
            'correo',
            'password',
            'confirmar_password',
            'rol_id',
        ] as $campo) {
            $valor = $this->request->getPost($campo);
            $entrada[$campo] = is_string($valor) ? $valor : '';
        }

        $entrada['nombre'] = trim($entrada['nombre']);
        $entrada['apellido'] = trim($entrada['apellido']);
        $entrada['correo'] = strtolower(trim($entrada['correo']));

        return $entrada;
    }

    private function volverAlFormulario(array $errores, array $entrada)
    {
        $entradaSegura = [
            'nombre' => $entrada['nombre'] ?? '',
            'apellido' => $entrada['apellido'] ?? '',
            'correo' => $entrada['correo'] ?? '',
            'rol_id' => $entrada['rol_id'] ?? '',
        ];

        return redirect()
            ->to(site_url('admin/usuarios/crear'))
            ->with('errors', $errores)
            ->with('empleadoInput', $entradaSegura);
    }

    private function esDuplicidadCorreo(DatabaseException $exception): bool
    {
        if ((int) $exception->getCode() === 1062) {
            return true;
        }

        $causa = $exception->getPrevious();

        return $causa !== null && (int) $causa->getCode() === 1062;
    }
}
