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

interface ProfesorForm {
    nombre: string;
    cedula: string;
    grados_asignados: string;
}

const form = useForm<ProfesorForm>({
    nombre: '',
    cedula: '',
    grados_asignados: '',
});

const onSubmit = () => {
    form.post('/profesor', {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            form.reset();
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
                <CirclePlus class="mr-2" /> Crear Profesor
            </Button>
        </DialogTrigger>

        <DialogContent class="sm:max-w-[50%]">
            <DialogHeader>
                <DialogTitle>Crear Profesor</DialogTitle>
                <DialogDescription>
                    Ingresa la información principal del profesor
                </DialogDescription>
            </DialogHeader>

            <form id="profesorForm" @submit.prevent="onSubmit" :class="{ 'opacity-50 pointer-events-none': form.processing }" class="grid gap-4 py-4">
                <div class="grid gap-2">
                    <Label for="nombre">Nombre</Label>
                    <Input id="nombre" v-model="form.nombre" type="text" placeholder="Ana Martinez" />
                    <p v-if="form.errors.nombre" class="text-sm font-medium text-destructive">{{ form.errors.nombre }}</p>
                </div>

                <div class="grid gap-2">
                    <Label for="cedula">Cédula</Label>
                    <Input id="cedula" v-model="form.cedula" type="text" placeholder="123456789" />
                    <p v-if="form.errors.cedula" class="text-sm font-medium text-destructive">{{ form.errors.cedula }}</p>
                </div>

                <div class="grid gap-2">
                    <Label for="grados_asignados">Grados asignados</Label>
                    <Input id="grados_asignados" v-model="form.grados_asignados" type="text" placeholder="6A, 7B, 8C" />
                    <p v-if="form.errors.grados_asignados" class="text-sm font-medium text-destructive">{{ form.errors.grados_asignados }}</p>
                </div>
            </form>

            <DialogFooter>
                <Button type="submit" form="profesorForm" :disabled="form.processing">
                    Guardar Profesor
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
