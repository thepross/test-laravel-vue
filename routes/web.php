<?php

use App\Http\Controllers\CuotaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PagoFacilWebHookController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use Illuminate\Http\Request;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    Route::get('/cuotas/create/{venta_id}', [CuotaController::class, 'createPago'])->name('cuotas.createPago');
    Route::get('/pagar/cuota/{cuota}', [PagoController::class, 'show'])->name('pagofacil.pagar.cuota');

    Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
    Route::get('/ventas/create', [VentaController::class, 'create'])->name('ventas.create');
    Route::post('/ventas', [VentaController::class, 'store'])->name('ventas.store');
    Route::get('/ventas/{venta}/show', [VentaController::class, 'show'])->name('ventas.show');
    Route::get('/ventas/{venta}/edit', [VentaController::class, 'edit'])->name('ventas.edit');
    Route::put('/ventas/{venta}', [VentaController::class, 'update'])->name('ventas.update');
    Route::delete('/ventas/{venta}', [VentaController::class, 'destroy'])->name('ventas.destroy');
});


Route::post('/pagofacil/callback', [PagoFacilWebHookController::class, 'callback'])
    ->name('pagofacil.callback');

Route::post('/puente/pagofacil/callback', function (Request $request) {
    // 1. Capturar los datos de PagoFácil
    $data = $request->all();
    // dd($data);

    // (Opcional) Loguear para debug
    Log::info('Callback recibido en Puente:', $data);

    // 2. URL de tu proyecto destino
    // Si estás en local, aquí pondrías tu URL de Ngrok: https://mi-tunnel.ngrok.io/api/pagofacil/webhook
    $targetUrl = 'https://mail.tecnoweb.org.bo/inf513/grupo05sc/estetica-laser/public/pagofacil/callback';

    // 3. Reenviar la petición (FORWARD)
    try {
        $response = Http::timeout(10)
            ->withHeaders([
                'X-Bridge-Secret' => 'secret123' // Para seguridad
            ])
            ->post($targetUrl, $data);

        Log::info('Reenviado con éxito. Respuesta: ' . $response->status());

        // 4. Responder a PagoFácil que todo salió bien
        return response()->json([
            'error' => 0,
            'status' => 1,
            'message' => 'Recibido y reenviado',
            'values' => true,
        ]);
    } catch (\Exception $e) {
        Log::error('Error reenviando: ' . $e->getMessage());
        // Aún así respondemos 200 a PagoFácil para que no siga reintentando infinitamente si el fallo es nuestro
        return response()->json(['status' => 0, 'message' => 'Error interno en puente'], 200);
    }
});


require __DIR__ . '/settings.php';
