<script setup lang="ts">
import { ref, } from 'vue'
import { Sun, Moon, Pencil } from 'lucide-vue-next'
import { Head, router, useForm, } from '@inertiajs/vue3';
import { IRoles, IUser, type BreadcrumbItem } from '@/types';
import Alert from '@/components/common/Alert.vue';
import CreateUser from '@/pages/administracion/user/CreateUser.vue'
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableCaption,
    TableRow
} from '@/components/ui/table'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/components/ui/dialog"
import {
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from "@/components/ui/form"
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
    TooltipTrigger,
} from "@/components/ui/tooltip"
import { Input } from "@/components/ui/input"
import { Badge } from "@/components/ui/badge"
import { Switch } from "@/components/ui/switch"
import { Button } from "@/components/ui/button"
import AppLayout from '@/layouts/AppLayout.vue';
import { useFormEdit } from "@/schema/users/useUserSchema";
import { useResetFormOnClose } from '@/composables/useResetFormOnClose';
import { useCan } from '@/composables/useCan';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Administracion',
        href: '',
    },
    {
        title: 'Usuarios',
        href: '/administracion/usuarios',
    },
];

/**
 * Parametrizar informacion de usuario editable
 */
interface UserEditForm {
    id: number
    name: string
    email: string
    rol: IRoles[]
}

const isModalOpen = ref(false);
const role_user_select = ref<string[]>([])

const props = defineProps<{ users: IUser[], roles: IRoles[] }>();

const formEditData = useForm<UserEditForm>({
    id: 0,
    name: "",
    email: "",
    rol: [],
})

const { isFieldDirty, handleSubmit, setValues } = useFormEdit();

/**
 * Editar usuario
 */
const onSubmitEditUser = handleSubmit((values: any) => {

    formEditData.name = values.name;
    formEditData.email = values.email;

    formEditData.rol = values.rol?.map((id: string) => Number(id)) ?? []

    formEditData.put(`/user/${values.id}`, {
        preserveScroll: true,
        preserveState: true,
        replace: true,
        onSuccess: () => {
            formEditData.reset()
            isModalOpen.value = false;
        },
        onError: () => {
            console.log('fallo');
            console.log(
                {
                    rol: formEditData.rol = values.rol?.map((id: string) => Number(id)) ?? []
                }
            )
        },
    })

})

/**
 * Enviar id y ejecutar ruta que inhabilita o habilita user
 * @param id User
 */
const onToogleStatuUser = ((id: number) => {

    router.delete(`/user/${id}`, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            // console.log('onSuccess');
        },
        onError: () => {
            // console.log('onError');
        },
    })
});

/**
 * abrir modal
 * cargar informacion al modal
 * seleccionar roles de usuario
 */
const openDialogEditWithData = (data: IUser) => {

    isModalOpen.value = !isModalOpen.value;

    const roleOwnUser = new Set(data.roles.map(r => r.id));

    formEditData.id = data.id;
    formEditData.name = data.name
    formEditData.email = data.email
    formEditData.rol = props.roles.map(r => ({
        'isGiven': roleOwnUser.has(r.id), ...r
    }));

    role_user_select.value = formEditData.rol
        .filter(r => r.isGiven)
        .map(r => String(r.id)) ?? [];

    setValues({
        id: data.id,
        name: data.name,
        email: data.email,
        rol: role_user_select.value
    })
}

/**
 * Limpiando errores al momento de cerrar el modal
 */
useResetFormOnClose(isModalOpen, formEditData);

/**
 * Verificando permisos en el UI
 */
const { can } = useCan();
</script>

<template>

    <Head title="Usuarios"></Head>
    <Alert />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">

            <section class="flex">
                <CreateUser :roles v-if="can('create user')" />
            </section>

            <section
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <Table class="table-auto">
                    <TableCaption class="caption-bottom"> Lista de Usuarios </TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="font-medium text-center text-regal-blue"> # </TableHead>
                            <TableHead class="font-medium text-center text-regal-blue"> Nombre </TableHead>
                            <TableHead class="font-medium text-center text-regal-blue"> Correo </TableHead>
                            <TableHead class="font-medium text-center text-regal-blue"> Estado </TableHead>
                            <TableHead class="font-medium text-center text-regal-blue"> Rol </TableHead>
                            <TableHead class="font-medium text-center text-regal-blue"> Acciones </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>

                        <TableRow v-for="(user, index) in users" :key="user.id">
                            <TableCell class="font-medium text-center"> {{ index + 1 }}</TableCell>
                            <TableCell class="font-medium text-center"> {{ user.name }}</TableCell>
                            <TableCell class="font-medium text-center"> {{ user.email }} </TableCell>
                            <TableCell class="font-medium text-center"> {{ user.is_active_text }} </TableCell>
                            <TableCell class="font-medium text-center">
                                <Badge v-for="rol in user.roles" :key="rol.id" class="px-4 py-2 mx-1">
                                    {{ rol.name }}
                                    <br />
                                </Badge>
                            </TableCell>

                            <TableCell class="flex items-center space-x-2 justify-center">
                                <TooltipProvider v-if="can('update user')">
                                    <Tooltip>
                                        <TooltipTrigger as-child>
                                            <Button class="btn" variant="outline" @click="openDialogEditWithData(user)">
                                                <Pencil />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>Editar Usuario</p>
                                        </TooltipContent>
                                    </Tooltip>
                                </TooltipProvider>
                                <TooltipProvider v-if="can('delete user')">
                                    <Tooltip>
                                        <TooltipTrigger>
                                            <Switch class="btn" :model-value="!!(+user.is_active)"
                                                @update:model-value="onToogleStatuUser(user.id)">
                                                <template #thumb>
                                                    <Sun v-if="+user.is_active === 1" icon="lucide:sun"
                                                        class="size-5" />
                                                    <Moon v-else icon="lucide:moon" class="size-5" />
                                                </template>
                                            </Switch>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <span>Estado Usuario</span>
                                        </TooltipContent>
                                    </Tooltip>
                                </TooltipProvider>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </section>
        </div>

        <!-- Modal Edit -->
        <Dialog v-if="isModalOpen" :open="isModalOpen" @update:open="isModalOpen = $event">
            <DialogTrigger as-child>
                <Button variant="outline">
                    <Pencil />
                </Button>
            </DialogTrigger>
            <DialogContent class="sm:max-w-[50%]">
                <DialogHeader>
                    <DialogTitle>Editar Usuario</DialogTitle>
                    <DialogDescription>
                        Ingrese la informacion pertinente á editar del usuario
                    </DialogDescription>
                </DialogHeader>

                <form id="userEditForm" @submit.prevent="onSubmitEditUser" disableWhileProcessing
                    :class="{ 'opacity-50 pointer-events-none': formEditData.processing }"
                    className="inert:opacity-50 inert:pointer-events-none grid gap-4 py-4">
                    <FormField v-slot="{ componentField }" name="name" class="grid grid-cols-4 items-center gap-4"
                        :validate-on-change="!isFieldDirty">
                        <FormItem>
                            <FormLabel>Nombre</FormLabel>
                            <FormControl>
                                <Input type="text" placeholder="pedro perez" v-bind="componentField"
                                    v-model="formEditData.name" />
                                <p v-if="formEditData.errors.name" role="alert" id="reka-v-18-form-item-message"
                                    class="text-sm font-medium text-destructive">{{ formEditData.errors.name }}</p>
                            </FormControl>
                            <FormDescription>
                                Este es tu nombre de usuario.
                            </FormDescription>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <FormField name="email" v-slot="{ componentField }" class="grid grid-cols-4 items-center gap-4"
                        :validate-on-change="!isFieldDirty">
                        <FormItem>
                            <FormLabel>Email</FormLabel>
                            <FormControl>
                                <Input type="email" placeholder="pedro.perez@gmail.com" v-bind="componentField"
                                    v-model="formEditData.email" />
                                <p v-if="formEditData.errors.email" role="alert" id="reka-v-18-form-item-message"
                                    class="text-sm font-medium text-destructive">{{ formEditData.errors.email }}</p>
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
                                <Select class="col-span-3" v-bind="componentField" multiple v-model="role_user_select">
                                    <SelectTrigger class="col-span-3">
                                        <SelectValue placeholder="Seleccionar rol" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectLabel>Roles</SelectLabel>
                                            <SelectItem v-for="role in formEditData.rol" :key="role.id"
                                                :value="String(role.id)">
                                                {{ role.name }}
                                            </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                            </FormControl>
                            <FormDescription>
                                Estos son los roles asignados.
                            </FormDescription>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <DialogFooter>
                        <Button class="btn" type="submit" form="userEditForm" :disabled="formEditData.processing"
                            :class="{ 'opacity-50 cursor-not-allowed': formEditData.processing }">
                            Editar Usuario
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

    </AppLayout>
</template>
