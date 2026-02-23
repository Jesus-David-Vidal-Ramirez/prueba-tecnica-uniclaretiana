<template>
    <section>
        <transition name="fade">
            <div v-if="showToast" :class="toastClasses">
                <component :is="toastIcon" class="h-5 w-5"
                    :class="`text-${toastColor}-600 dark:text-${toastColor}-400`" />

                <span>{{ toastMessage }}</span>

                <button @click="hideToast"
                    class="ml-2 rounded-full p-1 hover:bg-black/10 dark:hover:bg-white/10 transition-colors">
                    <CircleX class="h-4 w-4" />
                </button>
            </div>
        </transition>
    </section>
</template>

<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { ref, watch, computed, onMounted } from 'vue';
import { CircleCheckBig, CircleX, AlertTriangle, Info } from 'lucide-vue-next';
import type { AlertCrud } from '@/types/alert';
import { EcolorType } from '@/types/alert';

const page = usePage();

// Estado interno del toast
const showToast = ref(false);
const toastMessage = ref('');
const toastColor = ref<'green' | 'red' | 'yellow' | 'indigo'>('indigo');
const toastType = ref<'success' | 'error' | 'warning' | 'info'>('info');

// Mapeo de iconos según tipo
const iconMap = {
    success: CircleCheckBig,
    error: CircleX,
    warning: AlertTriangle,
    info: Info,
    infos: Info,
};

const alertMessage = computed(() => page.props.alert);

// Watch al prop global alert
watch(() => page.props.alert, (newVal) => {
    if (newVal) {
        showToastNotification(newVal.message, newVal.type);
    }
});

onMounted(() => {
    if (alertMessage.value) {
        showToastNotification(alertMessage.value.message, alertMessage.value.type);
    }
});


const showToastNotification = (message: string, type: AlertCrud['type']) => {
    if(message !== ""){
        toastMessage.value = message;
        toastType.value = type;
        toastColor.value = EcolorType[type];// map a enum
        
        showToast.value = true;
        setTimeout(() => hideToast(), 4000);
    }
};

const hideToast = () => {
    showToast.value = false;
};

// Computed para clases dinámicas
const toastClasses = computed(() => [
    `bg-${toastColor.value}-50 border-${toastColor.value}-200 text-${toastColor.value}-800
    dark:bg-${toastColor.value}-900/20 dark:border-${toastColor.value}-800 dark:text-${toastColor.value}-200 
    fixed top-4 right-4 z-50 flex items-center gap-3 rounded-lg border px-4 py-3 shadow-lg backdrop-blur-sm`
]);

const toastIcon = computed(() => iconMap[toastType.value]);
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>