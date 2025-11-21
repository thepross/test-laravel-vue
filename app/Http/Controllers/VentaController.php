<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ventas = Venta::all();
        return Inertia::render('Ventas/Index', [
            'ventas' => $ventas,
        ]);
    }

    public function create()
    {
        return Inertia::render('Ventas/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required',
            'concepto' => 'required',
            'total' => 'required',
        ]);

        Venta::create([
            'cliente_id' => $request->cliente_id,
            'concepto' => $request->concepto,
            'total' => $request->total,
        ]);

        return redirect()->route('ventas.index')->with('success', 'Venta creada correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Venta $venta)
    {
        $venta->load([
            'cliente',
            'cuotas' => function ($query) {
                $query->orderBy('numero_cuota', 'asc');
            }
        ]);
        return Inertia::render('Ventas/Show', [
            'venta' => $venta,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Venta $venta)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Venta $venta)
    {
        //
    }
}
