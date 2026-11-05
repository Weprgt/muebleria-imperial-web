<?php

namespace App\Database\Seeds;

use App\Models\RolModel;
use App\Models\UsuarioModel;
use CodeIgniter\Database\Seeder;
use RuntimeException;

class AdministradorSeeder extends Seeder
{
    public function run()
    {
        $nombre   = env('ADMIN_NOMBRE');
        $apellido = env('ADMIN_APELLIDO');
        $correo   = env('ADMIN_CORREO');
        $password = env('ADMIN_PASSWORD');

        if (
            ! is_string($nombre) || trim($nombre) === '' ||
            ! is_string($apellido) || trim($apellido) === '' ||
            ! is_string($correo) || ! filter_var($correo, FILTER_VALIDATE_EMAIL) ||
            ! is_string($password) || strlen($password) < 12
        ) {
            throw new RuntimeException(
                'Configura ADMIN_NOMBRE, ADMIN_APELLIDO, ADMIN_CORREO '
                . 'y ADMIN_PASSWORD (mínimo 12 caracteres) en .env.'
            );
        }

        $rolModel = new RolModel();
        $usuarioModel = new UsuarioModel();

        $rolAdministrador = $rolModel
            ->where('nombre', 'Administrador')
            ->first();

        if ($rolAdministrador === null) {
            throw new RuntimeException(
                'No existe el rol Administrador. Ejecuta primero RolesSeeder.'
            );
        }

        if ($usuarioModel->where('correo', $correo)->first() !== null) {
            throw new RuntimeException(
                'Ya existe un usuario con ese correo. No se creó otra cuenta.'
            );
        }

        $resultado = $usuarioModel->insert([
            'rol_id'       => $rolAdministrador['id'],
            'nombre'       => trim($nombre),
            'apellido'     => trim($apellido),
            'correo'       => trim($correo),
            'password_hash'=> password_hash($password, PASSWORD_DEFAULT),
            'activo'       => 1,
        ]);

        if ($resultado === false) {
            throw new RuntimeException(
                'No se pudo crear el administrador: '
                . implode('; ', $usuarioModel->errors())
            );
        }
    }
}
