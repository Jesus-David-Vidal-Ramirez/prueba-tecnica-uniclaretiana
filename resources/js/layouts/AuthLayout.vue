<script setup lang="ts">
// import AuthLayout from '@/layouts/auth/AuthSimpleLayout.vue';
import AuthSplitLayout from '@/layouts/auth/AuthSplitLayout.vue';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <AuthSplitLayout :title="title" :description="description">
        <slot />
    </AuthSplitLayout>
</template>
