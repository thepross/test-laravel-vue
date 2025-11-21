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
        title: 'Editar un producto',
        href: '/products/edit',
    },
];

interface Product {
    id: number;
    name: string;
    description: string;
    price: string;
}
const props = defineProps<{ product: Product; }>();

const form = useForm({
    name: props.product.name,
    description: props.product.description,
    price: props.product.price,
});

const handleSubmit = () => {
    form.put(route('products.update', props.product.id));
};

</script>

<template>

    <Head title="Editar un producto" />

    <AppLayout :breadcrumbs="[{ title: 'Editar un producto', href: `/products/${props.product.id}/edit` }]">
        <div class="rounded-xl p-4">
            Editar un producto
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
                    <Button type="submit" :disabled="form.processing">Editar producto</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
