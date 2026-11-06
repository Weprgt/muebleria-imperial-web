<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Primero comprobamos que exista una sesión válida.
        if (! session()->get('logueado')) {
            return redirect()->to('/login');
        }

        // Obtenemos el rol del usuario autenticado.
        $rolId = (int) session()->get('rol_id');

        // Los roles permitidos se recibirán desde Routes.php.
        $rolesPermitidos = array_map('intval', $arguments ?? []);

        // Sin roles configurados, denegamos el acceso.
        if (
            empty($rolesPermitidos) ||
            ! in_array($rolId, $rolesPermitidos, true)
        ) {
            return service('response')
                ->setStatusCode(403)
                ->setBody('403 - Acceso denegado.');
        }

        // Si el rol está autorizado, continúa la solicitud.
        return null;
    }

    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {
        // No necesitamos modificar la respuesta.
    }
}
