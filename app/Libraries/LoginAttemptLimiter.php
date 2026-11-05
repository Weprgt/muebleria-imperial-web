<?php

namespace App\Libraries;

use RuntimeException;

class LoginAttemptLimiter
{
    private const MAX_INTENTOS = 5;
    private const VENTANA_SEGUNDOS = 900;

    public function ejecutar(
        string $correo,
        string $ip,
        callable $verificarCredenciales
    ): array {
        $clave = hash('sha256', strtolower(trim($correo)) . '|' . $ip);

        $ruta = WRITEPATH . 'locks/login_' . $clave . '.lock';

        $archivo = fopen($ruta, 'c');

        if ($archivo === false) {
            throw new RuntimeException('No se pudo abrir el bloqueo.');
        }

        try {
            if (! flock($archivo, LOCK_EX)) {
                throw new RuntimeException('No se pudo obtener el bloqueo.');
            }

            $cache = service('cache');
            $claveCache = 'login_intentos_' . $clave;

            $registro = $cache->get($claveCache);

            if (
                ! is_array($registro) ||
                ! isset($registro['intentos'], $registro['vence_en']) ||
                (int) $registro['vence_en'] <= time()
            ) {
                $registro = [
                    'intentos' => 0,
                    'vence_en' => time() + self::VENTANA_SEGUNDOS,
                ];
            }

            if ($registro['intentos'] >= self::MAX_INTENTOS) {
                return ['estado' => 'bloqueado'];
            }

            $valido = $verificarCredenciales();

            if ($valido) {
                $cache->delete($claveCache);
                return ['estado' => 'correcto'];
            }

            $registro['intentos']++;

            $restante = max(1, $registro['vence_en'] - time());

            if (! $cache->save($claveCache, $registro, $restante)) {
                throw new RuntimeException('No se pudo guardar el contador.');
            }

            return ['estado' => 'incorrecto'];
        } finally {
            flock($archivo, LOCK_UN);
            fclose($archivo);
        }
    }
}
