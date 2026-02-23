<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { IRoles, type BreadcrumbItem } from '@/types';
import { Head, router, useForm, } from '@inertiajs/vue3';
import Alert from '@/components/common/Alert.vue';
import CreateRol from '@/pages/administracion/rol/CreateRol.vue';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableCaption,
    TableRow
} from '@/components/ui/table'
import { Eye, Pencil, Trash } from 'lucide-vue-next';
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
import { Input } from "@/components/ui/input"
import { Checkbox } from "@/components/ui/checkbox"
import { Label } from "@/components/ui/label"
import { Button } from "@/components/ui/button"
import { ref, } from 'vue';
import { useRolFormEdit } from '@/schema/rol/useRolSchema';
import { IPermission } from '@/types/permission';
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
        title: 'Roles',
        href: '/administracion/rol',
    },
];

/**
 * Parametrizar informacion del rol editable
 */
interface RolEditForm {
    id: number
    name: string
}

const isModalOpen = ref(false);
const isModalOpenPermission = ref(false);
const props = defineProps<{ roles: IRoles[], permisos: IPermission[], rol: IRoles, }>();

const formEditData = useForm<RolEditForm>({
    id: 0,
    name: "",
})

const { isFieldDirty, handleSubmit, setValues } = useRolFormEdit();

/**
 * Editar rol
 */
const onSubmitEditRol = handleSubmit((values: RolEditForm) => {

    formEditData.id = values.id;
    formEditData.name = values.name.toLowerCase();

    formEditData.put(`/rol/${values.id}`, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            formEditData.reset()
            isModalOpen.value = false;
        },
        onError: () => {
            // el modal se mantiene abierto y los errores llegan en form.errors
        },
    })

})

/**
 * Enviar id y ejecutar ruta que inhabilita o habilita user
 * @param id User
 */
const onSubmitDeleteRol = ((id: number) => {

    router.delete(`/rol/${id}`, {
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
 * Enviar id y ejecutar ruta que inhabilita o habilita user
 * @param id User
 */
const onSubmitSearhcPermissionRol = ((id: number) => {

    router.get(`/rol/${id}`, {}, {
        preserveState: true,
        replace: true,
        onSuccess: () => {
            isModalOpenPermission.value = !isModalOpenPermission.value;
        }
    })
});

/**
 * abrir modal
 * cargar informacion al modal
 * seleccionar roles de usuario
 */
const openDialogEditWithData = (data: IRoles) => {

    isModalOpen.value = !isModalOpen.value;

    formEditData.id = data.id;
    formEditData.name = data.name;

    setValues({
        id: data.id,
        name: data.name,
    })
}

/**
 * abrir modal permisos por rol
 * cargar informacion de los permisos al modal
 */
const openDialogEditPermissionRol = (data: IRoles) => {
    onSubmitSearhcPermissionRol(data.id);
}

/**
 * Abrir modal y cargar los permisos que el rol tenga
 */
const onSubmitEditPermissionRol = () => {

    router.post(`/rol/${props.rol.id}/permissions`, {
        permisos: props.permisos.filter(p => p.checked).map(p => p.id),
    }, {
        onSuccess: () => {
            isModalOpenPermission.value = !isModalOpenPermission.value;
        }
    });

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

    <Head title="Roles"></Head>
    <Alert />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">

            <section class="flex">
                <CreateRol v-if="can('create roles')" />
            </section>

            <section
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <div class="w-full overflow-x-auto">
                    <Table class="table-auto min-w-[520px]">
                        <TableCaption> Lista de Roles </TableCaption>
                        <TableHeader> <TableRow>
                                <TableHead class="font-medium text-center text-regal-blue"> # </TableHead>
                                <TableHead class="font-medium text-center text-regal-blue"> Rol </TableHead>
                                <TableHead class="font-medium text-center text-regal-blue"> Acciones </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>

                            <TableRow v-for="(rol, index) in props.roles" :key="rol.id">
                                <TableCell class="font-medium text-center"> {{ index + 1 }}</TableCell>
                                <TableCell class="font-medium text-center"> {{ rol.name }}</TableCell>
                                <TableCell class="flex items-center space-x-2 justify-center">
                                    <TooltipProvider v-if="can('update roles')">
                                        <Tooltip>
                                            <TooltipTrigger as-child>
                                                <Button class="btn" variant="outline" @click="openDialogEditPermissionRol(rol)">
                                                    <Eye />
                                                </Button>
                                            </TooltipTrigger>
                                            <TooltipContent>
                                                <p>Observar Permisos del Rol</p>
                                            </TooltipContent>
                                        </Tooltip>
                                    </TooltipProvider>
                                    <TooltipProvider v-if="can('update roles')">
                                        <Tooltip>
                                            <TooltipTrigger as-child>
                                                <Button class="btn" variant="outline" @click="openDialogEditWithData(rol)">
                                                    <Pencil />
                                                </Button>
                                            </TooltipTrigger>
                                            <TooltipContent>
                                                <p>Editar Rol</p>
                                            </TooltipContent>
                                        </Tooltip>
                                    </TooltipProvider>
                                    <TooltipProvider v-if="can('delete roles')">
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
                                                            Esta acción no se puede deshacer. Eliminará permanentemente este rol y eliminará sus datos de nuestros servidores.
                                                        </AlertDialogDescription>
                                                    </AlertDialogHeader>
                                                    <AlertDialogFooter>
                                                        <AlertDialogCancel class="btn">Cancel</AlertDialogCancel>
                                                        <AlertDialogAction class="btn" variant="outline" @click="onSubmitDeleteRol(rol.id)">Continue
                                                            <Trash />
                                                        </AlertDialogAction>
                                                    </AlertDialogFooter>
                                                    </AlertDialogContent>
                                                </AlertDialog>
                                                
                                            </TooltipTrigger>
                                            <TooltipContent>
                                                <span>Eliminar Rol</span>
                                            </TooltipContent>
                                        </Tooltip>
                                    </TooltipProvider>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
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
                    <DialogTitle>Editar Rol</DialogTitle>
                    <DialogDescription>
                        Ingrese la informacion pertinente á editar del rol
                    </DialogDescription>
                </DialogHeader>

                <form id="userEditForm" @submit.prevent="onSubmitEditRol" disableWhileProcessing
                    :class="{ 'opacity-50 pointer-events-none': formEditData.processing }"
                    className="inert:opacity-50 inert:pointer-events-none grid gap-4 py-4">
                    <FormField v-slot="{ componentField }" name="name" class="grid grid-cols-4 items-center gap-4"
                        :validate-on-change="!isFieldDirty">
                        <FormItem>
                            <FormLabel>Nombre</FormLabel>
                            <FormControl>
                                <Input type="text" placeholder="ADMINISTRADOR" v-bind="componentField" v-uppercase />
                                <p v-if="formEditData.errors.name" role="alert" id="reka-v-18-form-item-message"
                                    class="text-sm font-medium text-destructive">{{ formEditData.errors.name }}</p>
                            </FormControl>
                            <FormDescription>
                                Este es tu nombre del rol.
                            </FormDescription>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <DialogFooter>
                        <Button class="btn" type="submit" form="userEditForm" :disabled="formEditData.processing"
                            :class="{ 'opacity-50 cursor-not-allowed': formEditData.processing }">
                            Editar Rol
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Modal Edit Permisos Rol -->
        <Dialog v-if="isModalOpenPermission" :open="isModalOpenPermission"
            @update:open="isModalOpenPermission = $event">
            <DialogTrigger as-child>
                <Button class="btn" variant="outline">
                    <Pencil />
                </Button>
            </DialogTrigger>
            <DialogContent class="w-[95vw] max-w-[95vw] sm:max-w-[70%] max-h-[90vh] flex flex-col overflow-y-auto px-4 sm:px-6">
                <DialogHeader>
                    <DialogTitle>Editar Permisos del Rol</DialogTitle>
                    <DialogDescription>
                        Ingrese la informacion pertinente á editar del rol
                    </DialogDescription>
                </DialogHeader>
                <h3 class="text-sm sm:text-base font-medium break-words">{{ rol.name }}</h3>
                <form id="editPermissionRolForm" @submit.prevent="onSubmitEditPermissionRol"
                    className="inert:opacity-50 inert:pointer-events-none grid gap-4 py-2 sm:py-4">
                    <div class="max-h-[60vh] overflow-y-auto py-2 sm:py-4 pr-1">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-2 sm:gap-4">
                            <div class="flex items-start gap-2 rounded-md border p-2 sm:border-0 sm:p-0" v-for="permiso in props.permisos"
                                :key="permiso.id">
                                <Checkbox :id="`permiso-${permiso.id}`" v-model="permiso.checked" class="mt-0.5" />
                                <Label :for="`permiso-${permiso.id}`" class="text-sm cursor-pointer leading-tight break-words">{{ permiso.name
                                }}</Label>
                                <!-- data-[state=checked]:border-blue-600 data-[state=checked]:bg-blue-600 data-[state=checked]:text-white dark:data-[state=checked]:border-blue-700 dark:data-[state=checked]:bg-blue-700 -->
                            </div>
                        </div>
                    </div>

                    <DialogFooter>
                        <Button class="btn w-full sm:w-auto" type="submit" form="editPermissionRolForm">
                            Editar Permisos del Rol
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
