<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\User;
use App\Models\Venta;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $cliente = Cliente::create([
            'nombre' => 'Cliente de prueba',
            'ci_nit' => '1234567',
            'celular' => '5551234',
            'email' => 'cliente@prueba.com',
        ]);
        $venta = Venta::create([
            'cliente_id' => $cliente->id,
            'total' => 10,
            'concepto' => 'Venta de prueba',
        ]);
        Venta::create([
            'cliente_id' => $cliente->id,
            'total' => 20,
            'concepto' => 'Venta de prueba 2',
        ]);

        for ($i = 1; $i <= 2; $i++) {
            $venta->cuotas()->create([
                'numero_cuota' => $i,
                'monto' => 0.1,
                'estado' => 'pendiente',
            ]);
        }
    }
}
