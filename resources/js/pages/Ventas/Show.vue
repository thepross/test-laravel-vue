<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { Venta, EstadoPago } from '@/types/pagofacil';

// Definición de props tipada
const props = defineProps<{
    venta: Venta;
}>();


// Función con tipado de argumento y retorno
const formatoMoneda = (valor: number): string => {
    return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(valor);
};

// Función que recibe el tipo literal EstadoPago
const claseEstado = (estado: EstadoPago): string => {
    const clases: Record<EstadoPago, string> = {
        'pagado': 'bg-green-100 text-green-800',
        'procesando': 'bg-yellow-100 text-yellow-800',
        'pendiente': 'bg-gray-100 text-gray-800',
        'parcial': 'bg-blue-100 text-blue-800',
    };
    return clases[estado] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <div class="max-w-4xl mx-auto py-10 px-4">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Venta #{{ props.venta.id }}
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">{{ props.venta.concepto }}</p>
            </div>

            <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                <dl class="sm:divide-y sm:divide-gray-200">
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Cliente</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ props.venta.cliente.nombre }}
                        </dd>
                    </div>
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">CI / NIT</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ props.venta.cliente.ci_nit }}
                        </dd>
                    </div>
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Total Global</dt>
                        <dd class="mt-1 text-lg font-bold text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ formatoMoneda(props.venta.total) }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="max-w-4xl mx-auto py-10 px-4">

            <div v-if="$page.props.errors.error" class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4"
                role="alert">
                <p class="font-bold">Ocurrió un error</p>
                <p>{{ $page.props.errors.error }}</p>
            </div>
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            </div>

        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Plan de Pagos</h3>
            </div>
            <ul v-if="props.venta.cuotas" role="list" class="divide-y divide-gray-200">
                <li v-for="cuota in props.venta.cuotas" :key="cuota.id" class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col">
                            <p class="text-sm font-medium text-indigo-600 truncate">
                                Cuota {{ cuota.numero_cuota }}
                            </p>
                            <p class="text-sm text-gray-500">
                                Vencimiento: {{ cuota.fecha_vencimiento ?? 'N/A' }}
                            </p>
                        </div>

                        <div class="flex items-center">
                            <div class="mr-4 text-sm font-bold text-gray-900">
                                {{ formatoMoneda(cuota.monto) }}
                            </div>

                            <span
                                :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full mr-4', claseEstado(cuota.estado)]">
                                {{ cuota.estado.toUpperCase() }}
                            </span>

                            <div v-if="cuota.estado !== 'pagado'">
                                <Link :href="route('pagofacil.pagar.cuota', cuota.id)"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                                Pagar QR
                                </Link>
                            </div>
                            <div v-else>
                                <span class="text-green-600 text-sm font-bold">✓ Cancelado</span>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>