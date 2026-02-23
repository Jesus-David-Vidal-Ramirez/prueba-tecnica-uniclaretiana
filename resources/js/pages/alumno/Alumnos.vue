<script setup lang="ts">
import { ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Sun, Moon, Pencil } from 'lucide-vue-next';
import { type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import Alert from '@/components/common/Alert.vue';
import CreateAlumno from '@/pages/alumno/CreateAlumno.vue';
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

interface Alumno {
    id: number;
    nombre: string;
    cedula: string;
    grado: string;
    estado_prueba: string;
    is_active: boolean;
    is_active_text: string;
}

const props = defineProps<{
    alumnos: Alumno[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Alumnos', href: '/alumno' },
];

const isModalOpen = ref(false);

const formEdit = useForm({
    id: 0,
    nombre: '',
    cedula: '',
    grado: '',
    estado_prueba: 'Pendiente',
});

const openDialogEditWithData = (alumno: Alumno) => {
    formEdit.id = alumno.id;
    formEdit.nombre = alumno.nombre;
    formEdit.cedula = alumno.cedula;
    formEdit.grado = alumno.grado;
    formEdit.estado_prueba = alumno.estado_prueba;
    isModalOpen.value = true;
};

const onSubmitEdit = () => {
    formEdit.put(`/alumno/${formEdit.id}`, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            formEdit.reset();
            formEdit.estado_prueba = 'Pendiente';
            isModalOpen.value = false;
        },
    });
};

const onToggleStatus = (id: number) => {
    router.delete(`/alumno/${id}`, {
        preserveScroll: true,
        preserveState: true,
    });
};

useResetFormOnClose(isModalOpen, formEdit);
</script>

<template>
    <Head title="Alumnos" />
    <Alert />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <section class="flex">
                <CreateAlumno />
            </section>

            <section class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <Table class="table-auto">
                    <TableCaption class="caption-bottom"> Lista de Alumnos </TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="text-center">#</TableHead>
                            <TableHead class="text-center">Nombre</TableHead>
                            <TableHead class="text-center">Cédula</TableHead>
                            <TableHead class="text-center">Grado</TableHead>
                            <TableHead class="text-center">Estado de la prueba</TableHead>
                            <TableHead class="text-center">Estado</TableHead>
                            <TableHead class="text-center">Acciones</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="(alumno, index) in props.alumnos" :key="alumno.id">
                            <TableCell class="text-center">{{ index + 1 }}</TableCell>
                            <TableCell class="text-center">{{ alumno.nombre }}</TableCell>
                            <TableCell class="text-center">{{ alumno.cedula }}</TableCell>
                            <TableCell class="text-center">{{ alumno.grado }}</TableCell>
                            <TableCell class="text-center">{{ alumno.estado_prueba }}</TableCell>
                            <TableCell class="text-center">{{ alumno.is_active_text }}</TableCell>
                            <TableCell class="flex items-center justify-center gap-2">
                                <Button variant="outline" @click="openDialogEditWithData(alumno)">
                                    <Pencil class="size-4" />
                                </Button>
                                <Switch :model-value="!!alumno.is_active" @update:model-value="onToggleStatus(alumno.id)">
                                    <template #thumb>
                                        <Sun v-if="alumno.is_active" class="size-4" />
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
                    <DialogTitle>Editar Alumno</DialogTitle>
                    <DialogDescription>
                        Actualiza la información del alumno
                    </DialogDescription>
                </DialogHeader>

                <form id="alumnoEditForm" @submit.prevent="onSubmitEdit" class="grid gap-4 py-4">
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
                        <Label for="grado_edit">Grado</Label>
                        <Input id="grado_edit" v-model="formEdit.grado" type="text" />
                        <p v-if="formEdit.errors.grado" class="text-sm font-medium text-destructive">{{ formEdit.errors.grado }}</p>
                    </div>

                    <div class="grid gap-2">
                        <Label for="estado_prueba_edit">Estado de la prueba</Label>
                        <select id="estado_prueba_edit" v-model="formEdit.estado_prueba" class="h-10 rounded-md border border-input bg-background px-3 py-2 text-sm">
                            <option value="Pendiente">Pendiente</option>
                            <option value="En progreso">En progreso</option>
                            <option value="Finalizada">Finalizada</option>
                        </select>
                        <p v-if="formEdit.errors.estado_prueba" class="text-sm font-medium text-destructive">{{ formEdit.errors.estado_prueba }}</p>
                    </div>
                </form>

                <DialogFooter>
                    <Button type="submit" form="alumnoEditForm" :disabled="formEdit.processing">
                        Guardar cambios
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
