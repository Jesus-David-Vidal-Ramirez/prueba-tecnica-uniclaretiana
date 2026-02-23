<template>
    <Dialog v-model:open="open">
        <TooltipProvider>
            <Tooltip>
                <TooltipTrigger>
                    <DialogTrigger as-child>
                        <Button class="btn" variant="outline">
                            <CirclePlus /> Crear Rol
                        </Button>
                    </DialogTrigger>
                </TooltipTrigger>
                <TooltipContent>
                    <p>Crear Rol</p>
                </TooltipContent>
            </Tooltip>
        </TooltipProvider>

        <DialogContent class="sm:max-w-[50%]">
            <DialogHeader>
                <DialogTitle>Crear Rol</DialogTitle>
                <DialogDescription>
                    Ingrese la informacion pertinente del rol
                </DialogDescription>
            </DialogHeader>

            <form id="rolForm" @submit.prevent="onSubmitCreateRol" disableWhileProcessing
                :class="{ 'opacity-50 pointer-events-none': form.processing }"
                className="inert:opacity-50 inert:pointer-events-none grid gap-4 py-4">
                <FormField v-slot="{ componentField }" name="name" :validate-on-blur="!isFieldDirty"
                    class="grid grid-cols-4 items-center gap-4">
                    <FormItem>
                        <FormLabel>Nombre Del Rol</FormLabel>
                        <FormControl>
                            <Input type="text" placeholder="ADMINISTRADOR" v-bind="componentField"
                            v-uppercase
                             />
                            <p v-if="form.errors.name" role="alert" id="reka-v-18-form-item-message"
                                class="text-sm font-medium text-destructive">{{ form.errors.name }}</p>
                        </FormControl>
                        <FormDescription>
                            <!-- Este es tu nombre del rol. -->
                        </FormDescription>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <DialogFooter>
                    <Button class="btn" type="submit" form="rolForm" :disabled="form.processing"
                        :class="{ 'opacity-50 cursor-not-allowed': form.processing }">
                        Agregar Rol
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">

import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/components/ui/dialog";
import {
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from "@/components/ui/form";
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger
} from '@/components/ui/tooltip';
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { CirclePlus } from 'lucide-vue-next';
import { useForm } from '@inertiajs/vue3';
import { useRolFormcreated } from "@/schema/rol/useRolSchema";
import { ref, } from "vue";
import { useResetFormOnClose } from "@/composables/useResetFormOnClose";

const { isFieldDirty, handleSubmit } = useRolFormcreated();
const open = ref(false);

interface RolForm {
    name: string
    guard_name: string
}

const form = useForm<RolForm>({
    name: "",
    guard_name: "",
})

const onSubmitCreateRol = handleSubmit((values) => {

    form.name = values.name.toLowerCase()
    form.guard_name = 'web'
    
    form.post('/rol', {
        // preserveScroll: true,
        // preserveState: true,
        onSuccess: () => {
            form.reset();
            open.value = false;
        },
        onError: () => {
            // el modal se mantiene abierto y los errores llegan en form.errors
            // console.log('Errores:', form.errors);
        },
    })

})

/**
 * Limpiando errores al momento de cerrar el modal
 */
useResetFormOnClose(open, form);

</script>

<style scoped></style>