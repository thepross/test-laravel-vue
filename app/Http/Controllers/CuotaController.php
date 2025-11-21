<?php

namespace App\Http\Controllers;

use App\Models\Cuota;
use App\Models\Venta;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CuotaController extends Controller
{

    public function createPago($venta_id)
    {
        $venta = Venta::findOrFail($venta_id);
        Cuota::create([
            'venta_id' => $venta_id,
            'numero_cuota' => 1,
            'fecha_vencimiento' => now()->addMonth(),
            'monto' => $venta->total,
            'estado' => 'pendiente',
        ]);
        return to_route('ventas.show', $venta_id);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Cuota $cuota)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cuota $cuota)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cuota $cuota)
    {
        //
    }
}
