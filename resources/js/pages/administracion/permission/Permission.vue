<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm, } from '@inertiajs/vue3';
import Alert from '@/components/common/Alert.vue';
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
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from "@/components/ui/tooltip"
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from '@/components/ui/alert-dialog'
import { Button } from "@/components/ui/button"
import { ref, } from 'vue';
import { IPermission } from '@/types/permission';
import { usePermissionFormEdit } from '@/schema/permission/usePermissionSchema';
import CreatePermission from './CreatePermission.vue';
import { Pencil, Trash } from 'lucide-vue-next';
import Input from '@/components/ui/input/Input.vue';
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
        title: 'Permisos',
        href: '/administracion/permisos',
    },
];

/**
 * Parametrizar informacion de usuario editable
 */
interface PermissionForm {
    id: number
    name: string
}

const isModalOpen = ref(false);
const props = defineProps<{ permissions: IPermission[] }>();

const formEditData = useForm<PermissionForm>({
    id: 0,
    name: "",
})

const { isFieldDirty, handleSubmit, setValues } = usePermissionFormEdit();

/**
 * Editar permission
 */
const onSubmitEditPermission = handleSubmit((values: PermissionForm) => {

    formEditData.id = values.id;
    formEditData.name = values.name.toLocaleLowerCase();

    formEditData.put(`/permission/${values.id}`, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            formEditData.reset()
            isModalOpen.value = false;
        },
        onError: () => {
        },
    })

})

/**
 * Enviar id y ejecutar ruta que inhabilita o habilita user
 * @param id permission
 */
const onSubmitDeletePermission = ((permission: PermissionForm) => {

    router.delete(`/permission/${permission.id}`, {
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
const openDialogEditWithData = (data: IPermission) => {

    isModalOpen.value = !isModalOpen.value;

    // const roleOwnUser = new Set(data.roles.map(r => r.id));

    formEditData.id = data.id;
    formEditData.name = data.name
    // formEditData.email = data.email
    // formEditData.rol = props.roles.map(r => ({
    //     'isGiven': roleOwnUser.has(r.id), ...r
    // }));

    // role_user_select.value = formEditData.rol
    //     .filter(r => r.isGiven)
    //     .map(r => String(r.id)) ?? [];

    setValues({
        id: data.id,
        name: data.name,
        // email: data.email,
        // rol: role_user_select.value
    })
}

/**
 * Limpiando errores al momento de cerrar el modal
 */
useResetFormOnClose(isModalOpen, formEditData)

/**
 * Verificando permisos en el UI
 */
const { can } = useCan();
</script>

<template>

    <Head title="Permisos"></Head>
    <Alert />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">

            <section class="flex">
                <CreatePermission v-if="can('create permission')" />
            </section>

            <section
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <Table class="table-auto">
                    <TableCaption> Lista de Permisos </TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="font-medium text-center text-regal-blue"> # </TableHead>
                            <TableHead class="font-medium text-center text-regal-blue"> Permiso </TableHead>
                            <TableHead class="font-medium text-center text-regal-blue"> Acciones </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>

                        <TableRow v-for="(permission, index) in props.permissions" :key="permission.id">
                            <TableCell class="font-medium text-center"> {{ index + 1 }}</TableCell>
                            <TableCell class="font-medium text-center"> {{ permission.name }}</TableCell>
                            <TableCell class="flex items-center space-x-2 justify-center">
                                <TooltipProvider v-if="can('update permission')">
                                    <Tooltip>
                                        <TooltipTrigger as-child>
                                            <Button class="btn" variant="outline" @click="openDialogEditWithData(permission)">
                                                <Pencil />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent >
                                            <p>Editar Permiso</p>
                                        </TooltipContent>
                                    </Tooltip>
                                </TooltipProvider>
                                <TooltipProvider v-if="can('delete permission')">
                                    <Tooltip>
                                        <TooltipTrigger>
                                            <AlertDialog>
                                                <AlertDialogTrigger as-child>
                                                <Button class="btn" variant="outline">
                                                    <Trash />
                                                </Button>
                                                </AlertDialogTrigger>
                                                <AlertDialogContent>
                                                <AlertDialogHeader>
                                                    <AlertDialogTitle>¿Estás completamente seguro?</AlertDialogTitle>
                                                    <AlertDialogDescription>
                                                        Esta acción no se puede deshacer. Eliminará permanentemente este permiso y eliminará sus datos de nuestros servidores.
                                                    </AlertDialogDescription>
                                                </AlertDialogHeader>
                                                <AlertDialogFooter>
                                                    <AlertDialogCancel class="btn">Cancel</AlertDialogCancel>
                                                    <AlertDialogAction class="btn" variant="outline" @click="onSubmitDeletePermission(permission)">Continue
                                                        <Trash />
                                                    </AlertDialogAction>
                                                </AlertDialogFooter>
                                                </AlertDialogContent>
                                            </AlertDialog>
                                            
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <span>Eliminar Permiso</span>
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
                    <DialogTitle>Editar Permiso</DialogTitle>
                    <DialogDescription>
                        Ingrese la informacion pertinente á editar del permiso
                    </DialogDescription>
                </DialogHeader>

                <form id="userEditForm" @submit.prevent="onSubmitEditPermission" disableWhileProcessing
                    :class="{ 'opacity-50 pointer-events-none': formEditData.processing }"
                    className="inert:opacity-50 inert:pointer-events-none grid gap-4 py-4">
                    <FormField v-slot="{ componentField }" name="name" class="grid grid-cols-4 items-center gap-4"
                        :validate-on-change="!isFieldDirty">
                        <FormItem>
                            <FormLabel>Nombre</FormLabel>
                            <FormControl>
                                <Input type="text" placeholder="Ver Articulos" v-bind="componentField" v-uppercase />
                                <p v-if="formEditData.errors.name" role="alert" id="reka-v-18-form-item-message"
                                    class="text-sm font-medium text-destructive">{{ formEditData.errors.name }}</p>
                            </FormControl>
                            <FormDescription>
                                Este es el nombre del permiso.
                            </FormDescription>
                            <FormMessage />
                        </FormItem>
                    </FormField>


                    <DialogFooter>
                        <Button class="btn" type="submit" form="userEditForm" :disabled="formEditData.processing"
                            :class="{ 'opacity-50 cursor-not-allowed': formEditData.processing }">
                            Editar Permiso
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
