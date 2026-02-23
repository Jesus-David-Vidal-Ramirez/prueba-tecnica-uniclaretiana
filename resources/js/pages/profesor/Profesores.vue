<script setup lang="ts">
import { ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Sun, Moon, Pencil } from 'lucide-vue-next';
import { type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import Alert from '@/components/common/Alert.vue';
import CreateProfesor from '@/pages/profesor/CreateProfesor.vue';
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Switch } from '@/components/ui/switch';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useResetFormOnClose } from '@/composables/useResetFormOnClose';

interface Profesor {
    id: number;
    nombre: string;
    cedula: string;
    grados_asignados: string;
    is_active: boolean;
    is_active_text: string;
}

const props = defineProps<{
    profesores: Profesor[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Profesores', href: '/profesor' },
];

const isModalOpen = ref(false);

const formEdit = useForm({
    id: 0,
    nombre: '',
    cedula: '',
    grados_asignados: '',
});

const openDialogEditWithData = (profesor: Profesor) => {
    formEdit.id = profesor.id;
    formEdit.nombre = profesor.nombre;
    formEdit.cedula = profesor.cedula;
    formEdit.grados_asignados = profesor.grados_asignados;
    isModalOpen.value = true;
};

const onSubmitEdit = () => {
    formEdit.put(`/profesor/${formEdit.id}`, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            formEdit.reset();
            isModalOpen.value = false;
        },
    });
};

const onToggleStatus = (id: number) => {
    router.delete(`/profesor/${id}`, {
        preserveScroll: true,
        preserveState: true,
    });
};

useResetFormOnClose(isModalOpen, formEdit);
</script>

<template>
    <Head title="Profesores" />
    <Alert />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <section class="flex">
                <CreateProfesor />
            </section>

            <section class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <Table class="table-auto">
                    <TableCaption class="caption-bottom"> Lista de Profesores </TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="text-center">#</TableHead>
                            <TableHead class="text-center">Nombre</TableHead>
                            <TableHead class="text-center">Cédula</TableHead>
                            <TableHead class="text-center">Grados asignados</TableHead>
                            <TableHead class="text-center">Estado</TableHead>
                            <TableHead class="text-center">Acciones</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="(profesor, index) in props.profesores" :key="profesor.id">
                            <TableCell class="text-center">{{ index + 1 }}</TableCell>
                            <TableCell class="text-center">{{ profesor.nombre }}</TableCell>
                            <TableCell class="text-center">{{ profesor.cedula }}</TableCell>
                            <TableCell class="text-center">{{ profesor.grados_asignados }}</TableCell>
                            <TableCell class="text-center">{{ profesor.is_active_text }}</TableCell>
                            <TableCell class="flex items-center justify-center gap-2">
                                <Button variant="outline" @click="openDialogEditWithData(profesor)">
                                    <Pencil class="size-4" />
                                </Button>
                                <Switch :model-value="!!profesor.is_active" @update:model-value="onToggleStatus(profesor.id)">
                                    <template #thumb>
                                        <Sun v-if="profesor.is_active" class="size-4" />
                                        <Moon v-else class="size-4" />
                                    </template>
                                </Switch>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </section>
        </div>

        <Dialog :open="isModalOpen" @update:open="isModalOpen = $event">
            <DialogContent class="sm:max-w-[50%]">
                <DialogHeader>
                    <DialogTitle>Editar Profesor</DialogTitle>
                    <DialogDescription>
                        Actualiza la información del profesor
                    </DialogDescription>
                </DialogHeader>

                <form id="profesorEditForm" @submit.prevent="onSubmitEdit" class="grid gap-4 py-4">
                    <div class="grid gap-2">
                        <Label for="nombre_edit">Nombre</Label>
                        <Input id="nombre_edit" v-model="formEdit.nombre" type="text" />
                        <p v-if="formEdit.errors.nombre" class="text-sm font-medium text-destructive">{{ formEdit.errors.nombre }}</p>
                    </div>

                    <div class="grid gap-2">
                        <Label for="cedula_edit">Cédula</Label>
                        <Input id="cedula_edit" v-model="formEdit.cedula" type="text" />
                        <p v-if="formEdit.errors.cedula" class="text-sm font-medium text-destructive">{{ formEdit.errors.cedula }}</p>
                    </div>

                    <div class="grid gap-2">
                        <Label for="grados_asignados_edit">Grados asignados</Label>
                        <Input id="grados_asignados_edit" v-model="formEdit.grados_asignados" type="text" />
                        <p v-if="formEdit.errors.grados_asignados" class="text-sm font-medium text-destructive">{{ formEdit.errors.grados_asignados }}</p>
                    </div>
                </form>

                <DialogFooter>
                    <Button type="submit" form="profesorEditForm" :disabled="formEdit.processing">
                        Guardar cambios
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
