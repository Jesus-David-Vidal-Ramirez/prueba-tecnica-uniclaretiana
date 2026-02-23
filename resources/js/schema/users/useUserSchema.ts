import * as z from "zod"
import { useForm } from "vee-validate"
import { toTypedSchema } from "@vee-validate/zod"

const formSchemaUserCreated = toTypedSchema(
    z.object({
        name: z.string({ message: 'Campo requerido' })
            .min(2, 'Este campo debe contener mas de 2 digito')
            .max(50, 'Este campo permite maximo 50 digito'),
        email: z.string({ message: 'Campo requerido' })
            .email({ message: 'Este correo no es valido' })
            .min(8, 'Este campo debe contener mas de 8 digito')
            .max(50, 'Este campo debe contener mas de 50 digito')
            .includes("@", { message: 'Este campo debe contener el simbolo de @' }),
        rol: z.array(z.number()).optional()
    })
)

const formSchemaUserEdit = toTypedSchema(
    z.object({
        id: z.number(),
        name: z.string({ message: 'Campo requerido' })
            .min(2, 'Este campo debe contener mas de 2 digito')
            .max(50, 'Este campo permite maximo 50 digito'),
        email: z.string({ message: 'Campo requerido' })
            .email({ message: 'Este correo no es valido' })
            .min(8, 'Este campo debe contener mas de 8 digito')
            .max(50, 'Este campo debe contener mas de 50 digito')
            .includes("@", { message: 'Este campo debe contener el simbolo de @' }),
        rol: z.array(z.string()).optional()
    })
)

const useFormcreated = () => {

    const { isFieldDirty, handleSubmit } = useForm({
        validationSchema: formSchemaUserCreated,
    })

    return { isFieldDirty, handleSubmit }
}

const useFormEdit = () => {

    const { isFieldDirty, handleSubmit, setValues } = useForm({
        validationSchema: formSchemaUserEdit,
        initialValues: {
            id: 0,
            name: '',
            email: '',
            rol: [],
        }
    })

    return { isFieldDirty, handleSubmit, setValues }
}


export {
    useFormcreated,
    useFormEdit
}