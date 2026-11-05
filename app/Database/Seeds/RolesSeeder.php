<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'nombre' => 'Administrador',
                'descripcion' => 'Gestiona usuarios, inventario y reportes.',
            ],
            [
                'nombre' => 'Ventas',
                'descripcion' => 'Gestiona ventas, pedidos y pagos.',
            ],
            [
                'nombre' => 'Taller',
                'descripcion' => 'Gestiona las reparaciones de muebles.',
            ],
            [
                'nombre' => 'Cliente',
                'descripcion' => 'Realiza pedidos y solicita reparaciones.',
            ],
        ];

        $builder = $this->db->table('roles');

        foreach ($roles as $rol) {
            $builder->ignore(true)->insert($rol);
        }
    }
}