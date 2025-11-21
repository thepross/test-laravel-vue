<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import Button from '@/components/ui/button/Button.vue';
import Alert from '@/components/ui/alert/Alert.vue';
import AlertTitle from '@/components/ui/alert/AlertTitle.vue';
import AlertDescription from '@/components/ui/alert/AlertDescription.vue';
import { Info } from 'lucide-vue-next';
import Table from '@/components/ui/table/Table.vue';
import TableCaption from '@/components/ui/table/TableCaption.vue';
import TableHeader from '@/components/ui/table/TableHeader.vue';
import TableRow from '@/components/ui/table/TableRow.vue';
import TableHead from '@/components/ui/table/TableHead.vue';
import TableBody from '@/components/ui/table/TableBody.vue';
import TableCell from '@/components/ui/table/TableCell.vue';


interface ProductProps {
    id: number;
    name: string;
    description: string;
    price: string;
}

const props = defineProps<{ products: ProductProps[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Productos',
        href: '/products',
    },
];

const page = usePage();

const handleDelete = (id: number) => {
    if (confirm('¿Estás seguro de que deseas eliminar este producto?')) {
        router.delete(route('products.destroy', { id }));
    }
}
</script>

<template>

    <Head title="Productos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-col gap-4 overflow-x-auto rounded-xl p-4">

            <div v-if="page.props.flash?.success">
                <Alert class="bg-green-200">
                    <Info class="h-6 w-6 text-green-500" />
                    <AlertTitle>Alerta</AlertTitle>
                    <AlertDescription>
                        {{ page.props.flash.success }}
                    </AlertDescription>
                </Alert>
            </div>

            <h1 class="text-2xl font-bold">Productos</h1>

            <Link :href="route('products.create')">
            <Button>Crear un producto</Button>
            </Link>

            <div>
                <Table>
                    <TableCaption>Lista de todos los productos.</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead>
                                ID
                            </TableHead>
                            <TableHead>Nombre</TableHead>
                            <TableHead>Descripción</TableHead>
                            <TableHead class="text-center">
                                Precio
                            </TableHead>
                            <TableHead class="text-center">
                                Acciones
                            </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="product in props.products" :key="product.id">
                            <TableCell class="text-sm">{{ product.id }}</TableCell>
                            <TableCell>{{ product.name }}</TableCell>
                            <TableCell>{{ product.description }}</TableCell>
                            <TableCell class="text-center">{{ product.price }}</TableCell>
                            <TableCell class="text-center gap-2 flex justify-center">
                                <Link :href="route('products.edit', product.id)">
                                <Button variant="outline" size="sm">Editar</Button>
                                </Link>
                                <Button variant="destructive" size="sm"
                                    @click="handleDelete(product.id)">Eliminar</Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

        </div>
    </AppLayout>
</template>
