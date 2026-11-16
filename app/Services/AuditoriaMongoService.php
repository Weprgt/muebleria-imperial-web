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
    public function obtenerRecientesPaginados(int $pagina = 1, int $porPagina = 5): array
    {
        if ($this->coleccion === null) {
            return [
                'disponible' => false,
                'eventos' => [],
                'total' => 0,
                'pagina' => 1,
                'totalPaginas' => 0,
            ];
        }

        try {
            $porPagina = max(1, min($porPagina, 5));
            $pagina = max(1, $pagina);
            $total = (int) $this->coleccion->countDocuments([]);
            $totalPaginas = (int) ceil($total / $porPagina);
            $paginaActual = min($pagina, max(1, $totalPaginas));

            $cursor = $this->coleccion->find(
                [],
                [
                    'sort'  => ['fecha' => -1, '_id' => -1],
                    'skip'  => ($paginaActual - 1) * $porPagina,
                    'limit' => $porPagina,
                ]
            );

            return [
                'disponible' => true,
                'eventos' => iterator_to_array($cursor, false),
                'total' => $total,
                'pagina' => $paginaActual,
                'totalPaginas' => $totalPaginas,
            ];

        } catch (Throwable $e) {
            log_message(
                'error',
                'Error al consultar auditoría MongoDB: ' . $e->getMessage()
            );

            return [
                'disponible' => false,
                'eventos' => [],
                'total' => 0,
                'pagina' => 1,
                'totalPaginas' => 0,
            ];
        }
    }
}
