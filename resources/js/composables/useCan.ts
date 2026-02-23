import { IPermission } from "@/types/permission";
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";

export const useCan = () => {
    const page = usePage();

    const permissions = computed(() => page.props.auth.permissions ?? [])

    const can = (params: string) => {

        return permissions.value.some((element: IPermission) => element.name === params);
    }

    return { can };

}
