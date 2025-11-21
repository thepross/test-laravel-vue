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
        title: 'Crear un producto',
        href: '/products/create',
    },
];

const form = useForm({
    name: '',
    description: '',
    price: '',
});

const handleSubmit = () => {
    form.post(route('products.store'));
};

</script>

<template>

    <Head title="Crear un producto" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="rounded-xl p-4">
            Crear un producto
            <form @submit.prevent="handleSubmit" action="" class="flex flex-col gap-4 mt-4">
                <div class="space-y-2">
                    <Label for="name">Nombre del producto</Label>
                    <Input v-model="form.name" id="name" name="name" type="text" placeholder="Nombre del producto" />
                    <div class="text-red-600" v-if="form.errors.name">{{ form.errors.name }}</div>
                </div>
                <div class="space-y-2">
                    <Label for="description">Descripción del producto</Label>
                    <Input v-model="form.description" id="description" name="description" type="text"
                        placeholder="Descripción del producto" />
                    <div class="text-red-600" v-if="form.errors.description">{{ form.errors.description }}</div>
                </div>
                <div class="space-y-2">
                    <Label for="price">Precio del producto</Label>
                    <Input v-model="form.price" id="price" name="price" type="text" placeholder="Precio del producto" />
                    <div class="text-red-600" v-if="form.errors.price">{{ form.errors.price }}</div>
                </div>
                <div class="flex items-center">
                    <Button type="submit" :disabled="form.processing">Crear producto</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
