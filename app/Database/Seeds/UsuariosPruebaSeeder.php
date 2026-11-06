<?php

namespace App\Database\Seeds;

use App\Models\UsuarioModel;
use CodeIgniter\Database\Seeder;
use RuntimeException;

class UsuariosPruebaSeeder extends Seeder
{
    public function run()
    {
        $password = env('PRUEBAS_PASSWORD');

        if (! is_string($password) || strlen($password) < 12) {
            throw new RuntimeException(
                'Configura PRUEBAS_PASSWORD en .env con al menos 12 caracteres.'
            );
        }

        $usuarios = [
            [
                'rol_id' => 2,
                'nombre' => 'Usuario',
                'apellido' => 'Ventas',
                'correo' => 'ventas.prueba@example.com',
            ],
            [
                'rol_id' => 3,
                'nombre' => 'Usuario',
                'apellido' => 'Taller',
                'correo' => 'taller.prueba@example.com',
            ],
            [
                'rol_id' => 4,
                'nombre' => 'Usuario',
                'apellido' => 'Cliente',
                'correo' => 'cliente.prueba@example.com',
            ],
        ];

        $modelo = new UsuarioModel();

        foreach ($usuarios as $usuario) {
            if ($modelo->where('correo', $usuario['correo'])->first() !== null) {
                continue;
            }

            $usuario['password_hash'] = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $usuario['activo'] = 1;

            if ($modelo->insert($usuario) === false) {
                throw new RuntimeException(
                    'No se pudo crear el usuario de prueba: '
                    . implode('; ', $modelo->errors())
                );
            }
        }
    }
}
