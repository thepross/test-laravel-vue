// resources/js/types/pagofacil.ts

// Definimos los estados posibles para evitar "magic strings"
export type EstadoPago = 'pendiente' | 'procesando' | 'pagado' | 'parcial';

export interface Cliente {
    id: number;
    nombre: string;
    ci_nit: string;
    celular: string;
    email: string | null;
    created_at?: string;
    updated_at?: string;
}

export interface Cuota {
    id: number;
    venta_id: number;
    numero_cuota: number;
    monto: number; // En JS los decimales son number
    fecha_vencimiento: string | null; // Laravel envía fechas como string ISO
    estado: EstadoPago;
    pagofacil_transaction_id: string | null;
    qr_image?: string | null; // Base64 string (opcional en el objeto cuota)

    // Relaciones opcionales (pueden venir cargadas o no)
    venta?: Venta;
    created_at?: string;
    updated_at?: string;
}

export interface Venta {
    id: number;
    cliente_id: number;
    concepto: string;
    total: number;
    estado: EstadoPago;

    // Relaciones
    cliente: Cliente;
    cuotas?: Cuota[]; // Array de cuotas

    created_at?: string;
    updated_at?: string;
}

// Tipos para las Props de las Vistas de Inertia
export interface VentaShowProps {
    venta: Venta; // La venta debe incluir cliente y cuotas
}

export interface PagoShowQrProps {
    cuota: Cuota; // La cuota debe incluir venta y cliente
    qrImage: string; // El string base64 directo
}
