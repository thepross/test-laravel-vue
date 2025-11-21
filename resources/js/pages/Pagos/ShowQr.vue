<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import type { Cuota } from '@/types/pagofacil';

// 1. Props (Recibimos la cuota inicial y la imagen)
const props = defineProps<{
    cuota: Cuota;
    qrImage: string;
    callbackUrl: string;
}>();

const cuotaActual = ref<Cuota>(props.cuota);

// Variable para controlar el intervalo del sondeo
let pollInterval: ReturnType<typeof setInterval> | null = null;
let countdownInterval: ReturnType<typeof setInterval> | null = null;
const isPolling = ref(false);
const isCountdown = ref(false);

// 2. Lógica de Polling (Verificación en tiempo real)
const startPolling = () => {
    // Si ya está pagado, no hacemos nada
    if (cuotaActual.value.estado === 'pagado') return; // Usamos la local

    isPolling.value = true;

    pollInterval = setInterval(() => {
        router.reload({
            only: ['cuota'], // IMPORTANTE: Solo recargamos el objeto 'cuota' para no saturar
            preserveScroll: true, // Mantiene la posición de la pantalla
            preserveState: true, // Mantiene el estado de componentes (inputs, etc)
            onSuccess: (page) => {
                // Verificamos el dato actualizado que viene del servidor
                const updatedCuota = (page.props as any).cuota as Cuota;
                cuotaActual.value = updatedCuota;

                // Si el estado cambió a 'pagado', detenemos el sondeo
                if (updatedCuota.estado === 'pagado') {
                    stopPolling();
                }
            },
            onError: () => stopPolling() // Paramos si hay error de red
        });
    }, 3000); // Consultar cada 3 segundos
};

const stopPolling = () => {
    if (pollInterval) {
        clearInterval(pollInterval);
        pollInterval = null;
    }
    isPolling.value = false;
};

const tiempoRestante = ref(300);

const formatoTiempo = (segundos: number): string => {
    const minutos = Math.floor(segundos / 60);
    const segundosRestantes = segundos % 60;
    return `${minutos}:${segundosRestantes < 10 ? '0' : ''}${segundosRestantes}`;
};

const actualizarTiempoRestante = () => {
    tiempoRestante.value--;
    if (tiempoRestante.value <= 0) {
        stopPolling();
        stopCountdown();
    }
};

const startCountdown = () => {
    tiempoRestante.value = 300; // 5 minutos
    countdownInterval = setInterval(actualizarTiempoRestante, 1000); // Actualizar cada segundo
    isCountdown.value = true;
};

const stopCountdown = () => {
    if (countdownInterval) {
        clearInterval(countdownInterval);
        countdownInterval = null;
    }
    isCountdown.value = false;
};

// 3. Ciclo de Vida del Componente
onMounted(() => {
    startPolling();
    startCountdown();
});

onUnmounted(() => {
    stopPolling(); // Limpieza obligatoria para evitar fugas de memoria
    stopCountdown();
});

// Helper de formato
const formatoMoneda = (valor: number): string => {
    return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(valor);
};
</script>

<template>
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">

        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Pago con QR
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                {{ props.cuota.venta?.concepto }} | {{ props.callbackUrl }}
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div
                class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10 text-center transition-all duration-500 ease-in-out">

                <div v-if="props.cuota.estado === 'pagado'" class="animate-fade-in-up">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
                        <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>

                    <h3 class="text-2xl leading-6 font-bold text-gray-900 mb-2">¡Pago Realizado!</h3>
                    <p class="text-gray-500 mb-6">
                        Su cuota #{{ cuotaActual.numero_cuota }} ha sido cancelada correctamente.
                    </p>

                    <div class="bg-gray-50 p-4 rounded-md mb-6">
                        <dl class="flex justify-between text-sm font-medium text-gray-600">
                            <dt>Transacción:</dt>
                            <dd class="text-gray-900 font-mono">{{ cuotaActual.pagofacil_transaction_id }}</dd>
                        </dl>
                        <dl class="flex justify-between text-sm font-medium text-gray-600 mt-2">
                            <dt>Fecha:</dt>
                            <dd class="text-gray-900">{{ new Date().toLocaleDateString() }}</dd>
                        </dl>
                    </div>

                    <Link v-if="props.cuota.venta" :href="route('ventas.show', props.cuota.venta.id)"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none ring-2 ring-offset-2 ring-indigo-500">
                    Volver al Plan de Pagos
                    </Link>
                </div>

                <div v-else>
                    <div class="mb-4 space-x-2">
                        <span class="px-3 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">
                            Cuota {{ props.cuota.numero_cuota }}
                        </span>

                        <span class="px-3 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">
                            Transacción ID: {{ props.cuota.pagofacil_transaction_id }}
                        </span>
                    </div>

                    <p class="text-sm text-gray-500 mb-6">
                        Escanea el siguiente código QR desde tu aplicación bancaria móvil.
                    </p>

                    <div class="flex justify-center mb-6 relative group">
                        <div
                            class="absolute -inset-1 bg-linear-to-r from-blue-400 to-indigo-500 rounded-lg blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200">
                        </div>

                        <img v-if="qrImage" :src="'data:image/png;base64,' + qrImage" alt="QR PagoFácil"
                            class="relative w-96 h-96 bg-white p-2 rounded-lg border border-gray-200 shadow-xl" />
                        <div v-else
                            class="relative w-96 h-96 bg-gray-100 flex flex-col items-center justify-center rounded-lg animate-pulse">
                            <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <span class="text-xs text-gray-400">Generando QR...</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-center mb-8 space-x-2">
                        <span class="relative flex h-3 w-3">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
                        </span>
                        <span class="text-sm text-indigo-600 font-medium animate-pulse">Esperando pago...</span>
                    </div>

                    <div class="flex items-center justify-center mb-8 space-x-2">
                        <span class="text-2xl font-bold text-indigo-600 animate-pulse">{{ formatoTiempo(tiempoRestante)
                            }}</span>
                    </div>



                    <div class="border-t border-gray-200 pt-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-500">Total a pagar:</span>
                            <span class="text-2xl font-bold text-gray-900">{{ formatoMoneda(props.cuota.monto) }}</span>
                        </div>
                        <div class="text-xs text-gray-400">
                            El código expira en 5 minutos.
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 text-center">
                <Link :href="route('ventas.show', props.cuota.venta?.id)"
                    class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                &larr; Cancelar y volver atrás
                </Link>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Animación suave para la aparición del check */
.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>