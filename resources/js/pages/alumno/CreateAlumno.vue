<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { CirclePlus } from 'lucide-vue-next';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useResetFormOnClose } from '@/composables/useResetFormOnClose';

const open = ref(false);

interface AlumnoForm {
    nombre: string;
    cedula: string;
    grado: string;
    estado_prueba: string;
}

const form = useForm<AlumnoForm>({
    nombre: '',
    cedula: '',
    grado: '',
    estado_prueba: 'Pendiente',
});

const onSubmit = () => {
    form.post('/alumno', {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            form.reset();
            form.estado_prueba = 'Pendiente';
            open.value = false;
        },
    });
};

useResetFormOnClose(open, form);
</script>

<template>
    <Dialog v-model:open="open">
        <DialogTrigger as-child>
            <Button class="btn" variant="outline">
                <CirclePlus class="mr-2" /> Crear Alumno
            </Button>
        </DialogTrigger>

        <DialogContent class="sm:max-w-[50%]">
            <DialogHeader>
                <DialogTitle>Crear Alumno</DialogTitle>
                <DialogDescription>
                    Ingresa la información principal del alumno
                </DialogDescription>
            </DialogHeader>

            <form
                id="alumnoForm"
                @submit.prevent="onSubmit"
                :class="{ 'opacity-50 pointer-events-none': form.processing }"
                class="grid gap-4 py-4"
            >
                <div class="grid gap-2">
                    <Label for="nombre">Nombre</Label>
                    <Input id="nombre" v-model="form.nombre" type="text" placeholder="Juan Perez" />
                    <p v-if="form.errors.nombre" class="text-sm font-medium text-destructive">{{ form.errors.nombre }}</p>
                </div>

                <div class="grid gap-2">
                    <Label for="cedula">Cédula</Label>
                    <Input id="cedula" v-model="form.cedula" type="text" placeholder="123456789" />
                    <p v-if="form.errors.cedula" class="text-sm font-medium text-destructive">{{ form.errors.cedula }}</p>
                </div>

                <div class="grid gap-2">
                    <Label for="grado">Grado</Label>
                    <Input id="grado" v-model="form.grado" type="text" placeholder="10A" />
                    <p v-if="form.errors.grado" class="text-sm font-medium text-destructive">{{ form.errors.grado }}</p>
                </div>

                <div class="grid gap-2">
                    <Label for="estado_prueba">Estado de la prueba</Label>
                    <select id="estado_prueba" v-model="form.estado_prueba" class="h-10 rounded-md border border-input bg-background px-3 py-2 text-sm">
                        <option value="Pendiente">Pendiente</option>
                        <option value="En progreso">En progreso</option>
                        <option value="Finalizada">Finalizada</option>
                    </select>
                    <p v-if="form.errors.estado_prueba" class="text-sm font-medium text-destructive">{{ form.errors.estado_prueba }}</p>
                </div>
            </form>

            <DialogFooter>
                <Button type="submit" form="alumnoForm" :disabled="form.processing">
                    Guardar Alumno
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
