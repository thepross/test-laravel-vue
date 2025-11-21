<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuota extends Model
{
    protected $fillable = [
        'venta_id',
        'numero_cuota',
        'monto',
        'fecha_vencimiento',
        'estado',
        'pagofacil_transaction_id',
        'qr_image',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
}
