<?php

namespace App\Http\Controllers;

use App\Models\Cuota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PagoFacilWebHookController extends Controller
{
    public function callback(Request $request)
    {
        Log::info('Webhook PagoFácil recibido:', $request->all());

        $data = $request->all();
        $pedidoID = $data['PedidoID'];

        $cuotaId = preg_match('/C(\d+)$/', $pedidoID, $matches) ? $matches[1] : $pedidoID;

        $cuota = Cuota::find($cuotaId);
        // dd($cuota, $data);
        if ($cuota /*&& $data['Estado'] === 'Pagado'*/) {
            $cuota->update([
                'estado' => 'pagado',
                // 'pagofacil_transaction_id' => $data['TransactionId'] ?? null,
                'updated_at' => now(),
            ]);

            $cuotasPendientes = $cuota->venta->cuotas()->where('estado', '!=', 'pagado')->count();

            if ($cuotasPendientes === 0) {
                $cuota->venta->update(['estado' => 'pagado']);
            } else {
                $cuota->venta->update(['estado' => 'parcial']);
            }
        }

        return response()->json([
            'error' => 0,
            'status' => 1,
            'message' => 'Cuota procesada correctamente',
            'values' => true,
        ]);
    }
}
