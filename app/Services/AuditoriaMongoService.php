<?php

namespace App\Services;

use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\BSON\UTCDateTime;
use Throwable;

class AuditoriaMongoService
{
    private ?Collection $coleccion = null;

    public function __construct()
    {
        try {
            $cliente = new Client(
                'mongodb://127.0.0.1:27017',
                [
                    'serverSelectionTimeoutMS' => 1500,
                    'connectTimeoutMS' => 1500,
                ]
            );

            $this->coleccion = $cliente
                ->selectDatabase('muebleria_imperial_logs')
                ->selectCollection('auditoria_eventos');

        } catch (Throwable $e) {
            log_message(
                'error',
                'Error al preparar auditoría MongoDB: ' . $e->getMessage()
            );
        }
    }

    public function registrar(
        string $modulo,
        string $accion,
        array $datos = []
    ): bool {
        if ($this->coleccion === null) {
            return false;
        }

        try {
            $this->coleccion->insertOne([
                'modulo' => $modulo,
                'accion' => $accion,
                'datos' => $datos,
                'fecha' => new UTCDateTime(),
            ]);

            return true;

        } catch (Throwable $e) {
            log_message(
                'error',
                'Error al registrar auditoría MongoDB: ' . $e->getMessage()
            );

            return false;
        }
    }
}
