<?php

namespace App\Http\Controllers;

use App\Models\Cuota;
use App\Services\PagoFacilService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PagoController extends Controller
{
    protected $pagoService;

    public function __construct(PagoFacilService $pagoService)
    {
        $this->pagoService = $pagoService;
    }

    public function show(Cuota $cuota)
    {
        $cuota->load('venta.cliente');
        if (!$cuota->qr_image && $cuota->estado !== 'pagado') {
            try {
                $resultado = $this->pagoService->generarQrParaCuota($cuota);
                // dd($resultado);
                $cuota->update([
                    'pagofacil_transaction_id' => $resultado['transactionId'],
                    'qr_image' => $resultado['qrBase64'],
                    'estado' => 'procesando',
                ]);
            } catch (\Exception $e) {
                return back()->withErrors(['error' => 'Error al conectar con PagoFácil: ' . $e->getMessage()]);
            }
        } else {
            // dd('Ya tiene QR o está pagado');
        }
        return Inertia::render('Pagos/ShowQr', [
            'cuota' => $cuota,
            'qrImage' => $cuota->qr_image,
            'callbackUrl' => route('pagofacil.callback')
        ]);
    }
}
