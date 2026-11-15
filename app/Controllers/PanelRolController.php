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
        $reparacionModel = new \App\Models\ReparacionModel();

        $pendientes = $reparacionModel
            ->whereIn('estado', ['SOLICITADA', 'EN_EVALUACION'])
            ->countAllResults();

        $enReparacion = $reparacionModel
            ->where('estado', 'EN_REPARACION')
            ->countAllResults();

        $finalizadas = $reparacionModel
            ->where('estado', 'FINALIZADA')
            ->countAllResults();

        return view('taller/dashboard', [
            'nombre'       => session()->get('nombre'),
            'pendientes'   => $pendientes,
            'enReparacion' => $enReparacion,
            'finalizadas'  => $finalizadas,
        ]);
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

