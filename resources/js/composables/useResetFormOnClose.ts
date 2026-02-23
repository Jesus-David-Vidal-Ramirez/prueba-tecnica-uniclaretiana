import { watch, Ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
type InertiaForm<T extends Record<string, any>> =
    ReturnType<typeof useForm<T>>

export function useResetFormOnClose<T extends Record<string, any>>(
    isOpen: Ref<boolean>,
    form: InertiaForm<T>
) {
    watch(isOpen, (open) => {
        if (!open) {
            form.clearErrors()
        }
    })
}
