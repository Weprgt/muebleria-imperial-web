<?php

namespace App\Controllers;

class PanelRolController extends BaseController
{
    public function administrador()
    {
        return $this->mostrarPanel('Administrador');
    }

    public function ventas()
    {
        return $this->mostrarPanel('Ventas');
    }

    public function taller()
    {
        return $this->mostrarPanel('Taller');
    }

    public function cliente()
    {
        return $this->mostrarPanel('Cliente');
    }

    private function mostrarPanel(string $rol)
    {
        return view('paneles/rol', [
            'rol'    => $rol,
            'nombre' => session()->get('nombre'),
        ]);
    }
}

