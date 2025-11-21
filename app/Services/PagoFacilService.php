<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PagoFacilService
{
    public function generarQrParaCuota($cuota)
    {
        $token = $this->getAccessToken();
        $cliente = $cuota->venta->cliente;

        $data = [
            'paymentMethod' => 4,
            'clientName' => $cliente->nombre,
            'documentType' => 1,
            'documentId' => $cliente->ci_nit,
            'phoneNumber' => $cliente->celular,
            'email' => $cliente->email ?? 'sin_correo@admin.com',
            'paymentNumber' => "V" . $cuota->venta->id . "-C" . $cuota->id,
            'amount' => $cuota->monto,
            'currency' => 2,
            'clientCode' => "CLI-" . $cliente->id,
            'callbackUrl' => route('pagofacil.callback'),
            // 'callbackUrl' => "https://thepross.xyz/pagos/callback",
            'orderDetail' => [
                [
                    'serial' => $cuota->id,
                    'product' => $cuota->venta->concepto . " (Cuota #" . $cuota->numero_cuota . ")",
                    'quantity' => 1,
                    'price' => $cuota->monto,
                    'discount' => 0,
                    'total' => $cuota->monto
                ]
            ]
        ];

        $response = Http::withToken($token)->post(config('services.pagofacil.base_url') . '/generate-qr', $data);
        if ($response->successful() && (int) $response->json('error') === 0) {
            return $response->json('values');
        }
        throw new \Exception('Error al generar el QR de Pago Facil: ' . $response->body());
    }

    protected function getAccessToken()
    {
        return Cache::remember('pagofacil_token', 600, function () {
            $response = Http::withHeaders([
                'tcTokenService' => config('services.pagofacil.service_token'),
                'tcTokenSecret' => config('services.pagofacil.secret_token'),
            ])->post(config('services.pagofacil.base_url') . '/login');
            return $response->json('values.accessToken');
        });
    }
}
