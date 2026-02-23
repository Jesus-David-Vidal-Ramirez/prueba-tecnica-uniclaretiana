<script setup lang="ts">
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';

interface DashboardStats {
    total_usuarios: number;
    usuarios_admin: number;
    usuarios_profesor: number;
    usuarios_alumno: number;
    total_alumnos: number;
    total_profesores: number;
}

interface GroupAnalytics {
    group: string;
    total: number;
    ganaron: number;
    desaprobaron: number;
    porcentaje_aprobacion: number;
}

interface DashboardAnalytics {
    profesor: {
        alumnos_asignados: number;
        ganadores_por_grupo: GroupAnalytics[];
        mejores_grados: GroupAnalytics[];
    };
    alumno: {
        ganaron: number;
        desaprobaron: number;
        pendientes: number;
    };
}

const props = defineProps<{
    currentRole: 'admin' | 'profesor' | 'alumno' | 'otro';
    stats: DashboardStats;
    analytics: DashboardAnalytics;
}>();

const maxByRoles = computed(() =>
    Math.max(
        props.stats.usuarios_admin,
        props.stats.usuarios_profesor,
        props.stats.usuarios_alumno,
        1,
    ),
);

const maxGanadoresPorGrupo = computed(() =>
    Math.max(...props.analytics.profesor.ganadores_por_grupo.map((item) => item.ganaron), 1),
);

const maxResultadosAlumno = computed(() =>
    Math.max(
        props.analytics.alumno.ganaron,
        props.analytics.alumno.desaprobaron,
        props.analytics.alumno.pendientes,
        1,
    ),
);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div v-if="currentRole === 'admin'" class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                    <p class="text-sm text-muted-foreground">Total usuarios</p>
                    <p class="mt-2 text-3xl font-bold">{{ stats.total_usuarios }}</p>
                </div>
                <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                    <p class="text-sm text-muted-foreground">Total alumnos</p>
                    <p class="mt-2 text-3xl font-bold">{{ stats.total_alumnos }}</p>
                </div>
                <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                    <p class="text-sm text-muted-foreground">Total profesores</p>
                    <p class="mt-2 text-3xl font-bold">{{ stats.total_profesores }}</p>
                </div>
            </div>

            <div v-else class="grid auto-rows-min gap-4 md:grid-cols-2">
                <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                    <p class="text-sm text-muted-foreground">Cantidad de alumnos</p>
                    <p class="mt-2 text-3xl font-bold">{{ stats.total_alumnos }}</p>
                </div>
                <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                    <p class="text-sm text-muted-foreground">Cantidad de profesores</p>
                    <p class="mt-2 text-3xl font-bold">{{ stats.total_profesores }}</p>
                </div>
            </div>

            <div class="rounded-xl border border-sidebar-border/70 p-5 md:min-h-min dark:border-sidebar-border">
                <h2 class="mb-4 text-lg font-semibold">Distribución de usuarios por rol</h2>

                <div class="space-y-4">
                    <div>
                        <div class="mb-1 flex items-center justify-between text-sm">
                            <span>Admin</span>
                            <span>{{ stats.usuarios_admin }}</span>
                        </div>
                        <div class="h-3 rounded-full bg-muted">
                            <div class="h-3 rounded-full bg-blue-600" :style="{ width: `${(stats.usuarios_admin / maxByRoles) * 100}%` }" />
                        </div>
                    </div>

                    <div>
                        <div class="mb-1 flex items-center justify-between text-sm">
                            <span>Profesor</span>
                            <span>{{ stats.usuarios_profesor }}</span>
                        </div>
                        <div class="h-3 rounded-full bg-muted">
                            <div class="h-3 rounded-full bg-emerald-600" :style="{ width: `${(stats.usuarios_profesor / maxByRoles) * 100}%` }" />
                        </div>
                    </div>

                    <div>
                        <div class="mb-1 flex items-center justify-between text-sm">
                            <span>Alumno</span>
                            <span>{{ stats.usuarios_alumno }}</span>
                        </div>
                        <div class="h-3 rounded-full bg-muted">
                            <div class="h-3 rounded-full bg-orange-600" :style="{ width: `${(stats.usuarios_alumno / maxByRoles) * 100}%` }" />
                        </div>
                    </div>
                </div>
            </div>

            <template v-if="currentRole === 'profesor' || currentRole === 'admin'">
                <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                    <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                        <p class="text-sm text-muted-foreground">Alumnos activos</p>
                        <p class="mt-2 text-3xl font-bold">{{ stats.total_alumnos }}</p>
                    </div>
                    <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                        <p class="text-sm text-muted-foreground">Alumnos asignados por grupos</p>
                        <p class="mt-2 text-3xl font-bold">{{ analytics.profesor.alumnos_asignados }}</p>
                    </div>
                    <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                        <p class="text-sm text-muted-foreground">Profesores activos</p>
                        <p class="mt-2 text-3xl font-bold">{{ stats.total_profesores }}</p>
                    </div>
                </div>

                <div class="rounded-xl border border-sidebar-border/70 p-5 dark:border-sidebar-border">
                    <h2 class="mb-4 text-lg font-semibold">Ganaron por grupo</h2>
                    <div v-if="analytics.profesor.ganadores_por_grupo.length === 0" class="text-sm text-muted-foreground">
                        No hay datos para mostrar.
                    </div>
                    <div v-else class="space-y-4">
                        <div v-for="item in analytics.profesor.ganadores_por_grupo" :key="item.group">
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span>{{ item.group }}</span>
                                <span>{{ item.ganaron }} / {{ item.total }}</span>
                            </div>
                            <div class="h-3 rounded-full bg-muted">
                                <div class="h-3 rounded-full bg-emerald-600" :style="{ width: `${(item.ganaron / maxGanadoresPorGrupo) * 100}%` }" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-sidebar-border/70 p-5 dark:border-sidebar-border">
                    <h2 class="mb-4 text-lg font-semibold">Mejores grados</h2>
                    <div v-if="analytics.profesor.mejores_grados.length === 0" class="text-sm text-muted-foreground">
                        No hay datos para mostrar.
                    </div>
                    <div v-else class="space-y-3">
                        <div
                            v-for="item in analytics.profesor.mejores_grados"
                            :key="`${item.group}-top`"
                            class="flex items-center justify-between rounded-lg border p-3 text-sm"
                        >
                            <p class="font-medium">{{ item.group }}</p>
                            <p>{{ item.porcentaje_aprobacion }}% aprobación ({{ item.ganaron }}/{{ item.total }})</p>
                        </div>
                    </div>
                </div>
            </template>

            <template v-if="currentRole === 'alumno' || currentRole === 'admin'">
                <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                    <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                        <p class="text-sm text-muted-foreground">Ganaron examen</p>
                        <p class="mt-2 text-3xl font-bold">{{ analytics.alumno.ganaron }}</p>
                    </div>
                    <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                        <p class="text-sm text-muted-foreground">Desaprobaron examen</p>
                        <p class="mt-2 text-3xl font-bold">{{ analytics.alumno.desaprobaron }}</p>
                    </div>
                    <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                        <p class="text-sm text-muted-foreground">Pendientes/en curso</p>
                        <p class="mt-2 text-3xl font-bold">{{ analytics.alumno.pendientes }}</p>
                    </div>
                </div>

                <div class="rounded-xl border border-sidebar-border/70 p-5 dark:border-sidebar-border">
                    <h2 class="mb-4 text-lg font-semibold">Resultado general de exámenes</h2>
                    <div class="space-y-4">
                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span>Ganaron</span>
                                <span>{{ analytics.alumno.ganaron }}</span>
                            </div>
                            <div class="h-3 rounded-full bg-muted">
                                <div class="h-3 rounded-full bg-emerald-600" :style="{ width: `${(analytics.alumno.ganaron / maxResultadosAlumno) * 100}%` }" />
                            </div>
                        </div>

                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span>Desaprobaron</span>
                                <span>{{ analytics.alumno.desaprobaron }}</span>
                            </div>
                            <div class="h-3 rounded-full bg-muted">
                                <div class="h-3 rounded-full bg-red-600" :style="{ width: `${(analytics.alumno.desaprobaron / maxResultadosAlumno) * 100}%` }" />
                            </div>
                        </div>

                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span>Pendientes / en curso</span>
                                <span>{{ analytics.alumno.pendientes }}</span>
                            </div>
                            <div class="h-3 rounded-full bg-muted">
                                <div class="h-3 rounded-full bg-amber-500" :style="{ width: `${(analytics.alumno.pendientes / maxResultadosAlumno) * 100}%` }" />
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </AppLayout>
</template>
