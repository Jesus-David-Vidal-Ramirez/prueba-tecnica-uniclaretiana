import * as z from "zod"
import { useForm } from "vee-validate"
import { toTypedSchema } from "@vee-validate/zod"

const formSchemaPermissionCreated = toTypedSchema(
    z.object({
        name: z.string({ message: 'Campo requerido' })
            .min(2, 'Este campo debe contener mas de 2 digito')
            .max(50, 'Este campo permite maximo 50 digito'),
    })
)

const formSchemaPermissionEdit = toTypedSchema(
    z.object({
        id: z.number(),
        name: z.string({ message: 'Campo requerido' })
            .min(2, 'Este campo debe contener mas de 2 digito')
            .max(50, 'Este campo permite maximo 50 digito'),
    })
)

const usePermissionFormCreated = () => {
    const { isFieldDirty, handleSubmit } = useForm({
        validationSchema: formSchemaPermissionCreated,
    })

    return { isFieldDirty, handleSubmit }
}

const usePermissionFormEdit = () => {
    const { isFieldDirty, handleSubmit, setValues } = useForm({
        validationSchema: formSchemaPermissionEdit,
        initialValues: {
            id: 0,
            name: '',
        }
    })

    return { isFieldDirty, handleSubmit, setValues }
}

export {
    usePermissionFormCreated,
    usePermissionFormEdit
}