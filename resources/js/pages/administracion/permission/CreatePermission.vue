<template>
    <Dialog v-model:open="open">
        <TooltipProvider>
            <Tooltip>
                <TooltipTrigger>
                    <DialogTrigger as-child>
                        <Button class="btn" variant="outline">
                            <CirclePlusIcon /> Crear Permisos
                        </Button>
                    </DialogTrigger>
                </TooltipTrigger>
                <TooltipContent>
                    <p>Crear Permisos</p>
                </TooltipContent>
            </Tooltip>
        </TooltipProvider>

        <DialogContent class="sm:max-w-[50%]">
            <DialogHeader>
                <DialogTitle>Crear Permisos</DialogTitle>
                <DialogDescription>
                    Ingrese la informacion pertinente del permisos
                </DialogDescription>
            </DialogHeader>

            <form id="userForm" @submit.prevent="onSubmitCreatePermission" disableWhileProcessing
                :class="{ 'opacity-50 pointer-events-none': form.processing }"
                className="inert:opacity-50 inert:pointer-events-none grid gap-4 py-4">
                <FormField v-slot="{ componentField }" name="name" :validate-on-blur="!isFieldDirty"
                    class="grid grid-cols-4 items-center gap-4">
                    <FormItem>
                        <FormLabel>Nombre Del Permiso</FormLabel>
                        <FormControl>
                            <Input type="text" placeholder="Permiso" v-bind="componentField"
                            v-uppercase />
                            <p v-if="form.errors.name" role="alert" id="reka-v-18-form-item-message"
                                class="text-sm font-medium text-destructive">{{ form.errors.name }}</p>
                        </FormControl>
                        <FormDescription>
                            <!-- Este es tu nombre del rol. -->
                        </FormDescription>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <!-- <FormField v-slot="{ componentField }" name="rol" class="grid grid-cols-4 items-center gap-4">
                    <FormItem>
                        <FormLabel>Rol</FormLabel>
                        <FormControl>
                            <Select class="col-span-3" v-bind="componentField" multiple>
                                <SelectTrigger class="col-span-3">
                                    <SelectValue placeholder="Seleccionar rol" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Roles</SelectLabel>
                                        <SelectItem v-for="role in props" :key="role.id" :value=role.id>
                                            {{ role.name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                        </FormControl>
                        <FormDescription>
                            Estos son los roles.
                        </FormDescription>
                        <FormMessage />
                    </FormItem>
                </FormField> -->

                <DialogFooter>
                    <Button class="btn" type="submit" form="userForm" :disabled="form.processing"
                        :class="{ 'opacity-50 cursor-not-allowed': form.processing }">
                        Agregar Permiso
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
import { CirclePlusIcon } from 'lucide-vue-next';
import { useForm } from '@inertiajs/vue3';
import { ref, } from "vue";
import { usePermissionFormCreated } from "@/schema/permission/usePermissionSchema";
import { useResetFormOnClose } from "@/composables/useResetFormOnClose";

const { isFieldDirty, handleSubmit } = usePermissionFormCreated();
const open = ref(false);

interface PermissionForm {
    name: string
    guard_name: string
}

const form = useForm<PermissionForm>({
    name: "",
    guard_name: "",
})

const onSubmitCreatePermission = handleSubmit((values) => {

    form.name = values.name.toLowerCase()
    form.guard_name = 'web'
    // form.rol = values.rol ?? []

    form.post('/permission', {
        preserveScroll: true,
        preserveState: true,
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
useResetFormOnClose(open, form)

</script>

<style scoped></style>