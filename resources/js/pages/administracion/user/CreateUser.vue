<template>
    <Dialog v-model:open="open">
        <TooltipProvider>
            <Tooltip>
                <TooltipTrigger>
                    <DialogTrigger as-child>
                        <Button class="btn" variant="outline">
                            <CirclePlus /> Crear Usuario
                        </Button>
                    </DialogTrigger>
                </TooltipTrigger>
                <TooltipContent>
                    <p>Crear Usuario</p>
                </TooltipContent>
            </Tooltip>
        </TooltipProvider>

        <DialogContent class="sm:max-w-[50%]">
            <DialogHeader>
                <DialogTitle>Crear Usuario</DialogTitle>
                <DialogDescription>
                    Ingrese la informacion pertinente del usuario
                </DialogDescription>
            </DialogHeader>

            <form id="userForm" @submit.prevent="onSubmit" disableWhileProcessing
                :class="{ 'opacity-50 pointer-events-none': form.processing }"
                className="inert:opacity-50 inert:pointer-events-none grid gap-4 py-4">
                <FormField v-slot="{ componentField }" name="name" :validate-on-blur="!isFieldDirty"
                    class="grid grid-cols-4 items-center gap-4">
                    <FormItem>
                        <FormLabel>Nombre</FormLabel>
                        <FormControl>
                            <Input type="text" placeholder="pedro perez" v-bind="componentField" />
                            <p v-if="form.errors.name" role="alert" id="reka-v-18-form-item-message"
                                class="text-sm font-medium text-destructive">{{ form.errors.name }}</p>
                        </FormControl>
                        <FormDescription>
                            Este es tu nombre de usuario.
                        </FormDescription>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField v-slot="{ componentField }" name="email" class="grid grid-cols-4 items-center gap-4">
                    <FormItem>
                        <FormLabel>Email</FormLabel>
                        <FormControl>
                            <Input type="email" placeholder="pedro.perez@gmail.com" v-bind="componentField" />
                            <p v-if="form.errors.email" role="alert" id="reka-v-18-form-item-message"
                                class="text-sm font-medium text-destructive">{{ form.errors.email }}</p>
                        </FormControl>
                        <FormDescription>
                            Este es tu email.
                        </FormDescription>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField v-slot="{ componentField }" name="rol" class="grid grid-cols-4 items-center gap-4">
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
                                        <SelectItem v-for="role in props.roles" :key="role.id" :value=role.id>
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
                </FormField>

                <DialogFooter>
                    <Button class="btn" type="submit" form="userForm" :disabled="form.processing"
                        :class="{ 'opacity-50 cursor-not-allowed': form.processing }">
                        Agregar Usuario
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
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger
} from '@/components/ui/tooltip';
import { Button } from "@/components/ui/button";
import { IRoles } from "@/types";
import { Input } from "@/components/ui/input";
import { CirclePlus } from 'lucide-vue-next';
import { useForm } from '@inertiajs/vue3';
import { useFormcreated } from "@/schema/users/useUserSchema";
import { ref } from "vue";
import { useResetFormOnClose } from "@/composables/useResetFormOnClose";

const { isFieldDirty, handleSubmit } = useFormcreated();
const open = ref(false);

interface UserForm {
    name: string
    email: string
    rol: number[]
}
const form = useForm<UserForm>({
    name: "",
    email: "",
    rol: [],
})

const onSubmit = handleSubmit((values) => {

    form.name = values.name
    form.email = values.email
    form.rol = values.rol ?? []

    form.post('/user', {
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

interface Props {
    roles: IRoles[];
}
const props = withDefaults(defineProps<Props>(), {
    roles: () => [],
});

/**
 * Limpiando errores al momento de cerrar el modal
 */
useResetFormOnClose(open, form)

</script>

<style scoped></style>