<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import Button from '@/components/ui/button/Button.vue';
import Label from '@/components/ui/label/Label.vue';
import Input from '@/components/ui/input/Input.vue';
import { useForm } from "@inertiajs/vue3";

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Crear una venta',
        href: '/ventas/create',
    },
];

const form = useForm({
    cliente_id: '',
    concepto: '',
    total: '',
    estado: '',
});

const handleSubmit = () => {
    form.post(route('ventas.store'));
};

</script>

<template>

    <Head title="Crear una venta" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="rounded-xl p-4">
            Crear una venta
            <form @submit.prevent="handleSubmit" action="" class="flex flex-col gap-4 mt-4">
                <div class="space-y-2">
                    <Label for="cliente_id">Cliente</Label>
                    <Input v-model="form.cliente_id" id="cliente_id" name="cliente_id" type="text"
                        placeholder="Cliente" />
                    <div class="text-red-600" v-if="form.errors.cliente_id">{{ form.errors.cliente_id }}</div>
                </div>
                <div class="space-y-2">
                    <Label for="concepto">Concepto</Label>
                    <Input v-model="form.concepto" id="concepto" name="concepto" type="text" placeholder="Concepto" />
                    <div class="text-red-600" v-if="form.errors.concepto">{{ form.errors.concepto }}</div>
                </div>
                <div class="space-y-2">
                    <Label for="total">Total</Label>
                    <Input v-model="form.total" id="total" name="total" type="text" placeholder="Total" />
                    <div class="text-red-600" v-if="form.errors.total">{{ form.errors.total }}</div>
                </div>
                <div class="space-y-2">
                    <Label for="estado">Estado</Label>
                    <Input v-model="form.estado" id="estado" name="estado" type="text" placeholder="Estado" />
                    <div class="text-red-600" v-if="form.errors.estado">{{ form.errors.estado }}</div>
                </div>
                <div class="flex items-center">
                    <Button type="submit" :disabled="form.processing">Crear venta</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
