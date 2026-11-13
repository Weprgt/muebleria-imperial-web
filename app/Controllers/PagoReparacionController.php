<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\PagoReparacionModel;
use Throwable;

class PagoReparacionController extends BaseController
{
    public function registrar(int $id)
    {
        if (
            session('logueado') !== true ||
            (int) session('rol_id') !== 4
        ) {
            return redirect()->to(site_url('login'))
                ->with('error', 'Debes ingresar como cliente.');
        }

        $cliente = (new ClienteModel())
            ->where('usuario_id', session('usuario_id'))
            ->first();

        if (!$cliente) {
            return redirect()->to(site_url('mis-reparaciones'))
                ->with('error', 'No se encontró tu perfil de cliente.');
        }

        $db = db_connect();
        $db->transBegin();

        try {
            // Bloquear la reparación para evitar pagos simultáneos.
            $consulta = $db->table('reparaciones')
                ->where('id', $id)
                ->where('cliente_id', $cliente['id'])
                ->getCompiledSelect();

            $reparacion = $db->query(
                $consulta . ' FOR UPDATE'
            )->getRowArray();

            if (!$reparacion) {
                throw new \RuntimeException(
                    'La reparación no existe o no te pertenece.'
                );
            }

            if ($reparacion['estado'] !== 'ACEPTADA') {
                throw new \RuntimeException(
                    'La reparación no admite el registro del anticipo.'
                );
            }

            // Convertir el anticipo requerido a centavos.
            $anticipo = (string) $reparacion['anticipo_requerido'];

            if (!preg_match('/^\d{1,10}\.\d{2}$/', $anticipo)) {
                throw new \RuntimeException(
                    'El anticipo requerido no es válido.'
                );
            }

            [$quetzales, $centavos] = explode('.', $anticipo);

            $anticipoCentavos =
                ((int) $quetzales * 100) + (int) $centavos;

            if ($anticipoCentavos <= 0) {
                throw new \RuntimeException(
                    'El anticipo debe ser mayor que cero.'
                );
            }

            $metodo = trim(
                (string) $this->request->getPost('metodo_pago')
            );

            if (!in_array(
                $metodo,
                ['EFECTIVO', 'TRANSFERENCIA', 'DEPOSITO'],
                true
            )) {
                throw new \RuntimeException(
                    'Método de pago no permitido.'
                );
            }

            $referencia = trim(
                (string) $this->request->getPost('referencia')
            );

            if (mb_strlen($referencia) > 150) {
                throw new \RuntimeException(
                    'La referencia es demasiado larga.'
                );
            }

            if (
                in_array($metodo, ['TRANSFERENCIA', 'DEPOSITO'], true)
                && $referencia === ''
            ) {
                throw new \RuntimeException(
                    'Debes indicar la referencia del pago.'
                );
            }

            // No permitir otro anticipo pendiente o verificado.
            $pagoExistente = $db->table('pagos_reparacion')
                ->where('reparacion_id', $id)
                ->whereIn('estado', ['PENDIENTE', 'VERIFICADO'])
                ->countAllResults();

            if ($pagoExistente > 0) {
                throw new \RuntimeException(
                    'Esta reparación ya tiene un anticipo pendiente o verificado.'
                );
            }

            $pagoId = (new PagoReparacionModel())->insert([
                'reparacion_id' => $id,
                'monto' => number_format(
                    $anticipoCentavos / 100,
                    2,
                    '.',
                    ''
                ),
                'metodo_pago' => $metodo,
                'referencia' => $referencia !== '' ? $referencia : null,
                'estado' => 'PENDIENTE',
                'registrado_por' => session('usuario_id'),
            ], true);

            if (!$pagoId || $db->transStatus() === false) {
                throw new \RuntimeException(
                    'No se pudo registrar el anticipo.'
                );
            }

            if ($db->transCommit() === false) {
                throw new \RuntimeException(
                    'No se pudo confirmar la operación.'
                );
            }

            return redirect()->to(site_url('mis-reparaciones'))
                ->with(
                    'success',
                    'Anticipo #' . $pagoId .
                    ' registrado. Pendiente de verificación.'
                );

        } catch (Throwable $e) {
            $db->transRollback();

            log_message(
                'error',
                'Error al registrar anticipo de reparación: ' .
                $e->getMessage()
            );

            return redirect()->to(site_url('mis-reparaciones'))
                ->with(
                    'error',
                    $e instanceof \RuntimeException
                        ? $e->getMessage()
                        : 'No fue posible registrar el anticipo.'
                );
        }
    }

    public function registrarSaldo(int $id)
    {
        if (
            session('logueado') !== true ||
            (int) session('rol_id') !== 4
        ) {
            return redirect()->to(site_url('login'))
                ->with('error', 'Debes ingresar como cliente.');
        }

        $cliente = (new ClienteModel())
            ->where('usuario_id', session('usuario_id'))
            ->first();

        if (!$cliente) {
            return redirect()->to(site_url('mis-reparaciones'))
                ->with('error', 'No se encontró tu perfil de cliente.');
        }

        $db = db_connect();

        try {
            $db->transBegin();

            // Bloquear la reparación antes de consultar sus pagos.
            $reparacion = $db->query(
                'SELECT id, cliente_id, estado,
                        presupuesto_total, anticipo_requerido
                FROM reparaciones
                WHERE id = ? AND cliente_id = ?
                FOR UPDATE',
                [$id, $cliente['id']]
            )->getRowArray();

            if (!$reparacion || $reparacion['estado'] !== 'FINALIZADA') {
                throw new \RuntimeException(
                    'La reparación no está disponible para pagar el saldo.'
                );
            }

            // Trabajar con centavos para evitar errores de redondeo.
            $aCentavos = static function (string $importe): int {
                if (!preg_match('/^\d{1,10}\.\d{2}$/', $importe)) {
                    throw new \RuntimeException('Importe inválido.');
                }

                [$quetzales, $centavos] = explode('.', $importe);

                return ((int) $quetzales * 100) + (int) $centavos;
            };

            $presupuesto = $aCentavos(
                (string) $reparacion['presupuesto_total']
            );

            $anticipo = $aCentavos(
                (string) $reparacion['anticipo_requerido']
            );

            if ($presupuesto <= 0 || $anticipo <= 0) {
                throw new \RuntimeException(
                    'El presupuesto o anticipo no es válido.'
                );
            }

            $pagos = $db->table('pagos_reparacion')
                ->select('monto')
                ->where('reparacion_id', $id)
                ->where('estado', 'VERIFICADO')
                ->get()
                ->getResultArray();

            $totalVerificado = 0;

            foreach ($pagos as $pago) {
                $totalVerificado += $aCentavos(
                    (string) $pago['monto']
                );
            }

            if ($totalVerificado < $anticipo) {
                throw new \RuntimeException(
                    'El anticipo todavía no está completamente verificado.'
                );
            }

            $saldo = $presupuesto - $totalVerificado;

            if ($saldo <= 0) {
                throw new \RuntimeException(
                    'Esta reparación no tiene saldo pendiente.'
                );
            }

            // Evitar solicitudes de pago simultáneas.
            $pagosPendientes = $db->table('pagos_reparacion')
                ->where('reparacion_id', $id)
                ->where('estado', 'PENDIENTE')
                ->countAllResults();

            if ($pagosPendientes > 0) {
                throw new \RuntimeException(
                    'Ya existe un pago pendiente de verificación.'
                );
            }

            $metodo = trim(
                (string) $this->request->getPost('metodo_pago')
            );

            if (!in_array(
                $metodo,
                ['EFECTIVO', 'TRANSFERENCIA', 'DEPOSITO'],
                true
            )) {
                throw new \RuntimeException(
                    'Método de pago no permitido.'
                );
            }

            $referencia = trim(
                (string) $this->request->getPost('referencia')
            );

            if (mb_strlen($referencia) > 150) {
                throw new \RuntimeException(
                    'La referencia es demasiado larga.'
                );
            }

            if (
                in_array($metodo, ['TRANSFERENCIA', 'DEPOSITO'], true)
                && $referencia === ''
            ) {
                throw new \RuntimeException(
                    'Debes indicar la referencia del pago.'
                );
            }

            $pagoId = (new PagoReparacionModel())->insert([
                'reparacion_id' => $id,
                'monto' => number_format($saldo / 100, 2, '.', ''),
                'metodo_pago' => $metodo,
                'referencia' => $referencia !== '' ? $referencia : null,
                'estado' => 'PENDIENTE',
                'registrado_por' => session('usuario_id'),
            ], true);

            if (!$pagoId || $db->transStatus() === false) {
                throw new \RuntimeException(
                    'No se pudo registrar el pago del saldo.'
                );
            }

            if ($db->transCommit() === false) {
                throw new \RuntimeException(
                    'No se pudo confirmar la operación.'
                );
            }

            return redirect()->to(site_url('mis-reparaciones'))
                ->with(
                    'success',
                    'Pago del saldo #' . $pagoId .
                    ' registrado. Pendiente de verificación.'
                );

        } catch (Throwable $e) {
            $db->transRollback();

            log_message(
                'error',
                'Error al registrar saldo de reparación #' .
                $id . ': ' . $e->getMessage()
            );

            return redirect()->to(site_url('mis-reparaciones'))
                ->with(
                    'error',
                    $e instanceof \RuntimeException
                        ? $e->getMessage()
                        : 'No fue posible registrar el saldo.'
                );
        }
    }

}
