<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos
        $permissionUserCreate = Permission::create(['name' => 'create user']);
        $permissionUserView = Permission::create(['name' => 'view user']);
        $permissionUserUpdate = Permission::create(['name' => 'update user']);
        $permissionUserDelete = Permission::create(['name' => 'delete user']);

        $permissionPermissionCreate = Permission::create(['name' => 'create permission']);
        $permissionPermissionView = Permission::create(['name' => 'view permission']);
        $permissionPermissionUpdate = Permission::create(['name' => 'update permission']);
        $permissionPermissionDelete = Permission::create(['name' => 'delete permission']);

        $permissionRolesCreate = Permission::create(['name' => 'create roles']);
        $permissionRolesView = Permission::create(['name' => 'view roles']);
        $permissionRolesUpdate = Permission::create(['name' => 'update roles']);
        $permissionRolesDelete = Permission::create(['name' => 'delete roles']);

        $permissionProfesorCreate = Permission::create(['name' => 'create profesor']);
        $permissionProfesorView = Permission::create(['name' => 'view profesor']);
        $permissionProfesorUpdate = Permission::create(['name' => 'update profesor']);
        $permissionProfesorDelete = Permission::create(['name' => 'delete profesor']);

        $permissionAlumnoCreate = Permission::create(['name' => 'create alumno']);
        $permissionAlumnoView = Permission::create(['name' => 'view alumno']);
        $permissionAlumnoUpdate = Permission::create(['name' => 'update alumno']);
        $permissionAlumnoDelete = Permission::create(['name' => 'delete alumno']);


        // Crear rol
        $roleAdmin = Role::create(['name' => 'administrador']);
        $roleProfesor = Role::create(['name' => 'profesor']);
        $roleAlumno = Role::create(['name' => 'alumno']);

        // Asignar permisos al rol
        $roleAdmin->givePermissionTo([$permissionUserCreate, $permissionUserView, $permissionUserUpdate, $permissionUserDelete]);
        $roleAdmin->givePermissionTo([$permissionPermissionCreate, $permissionPermissionView, $permissionPermissionUpdate, $permissionPermissionDelete]);
        $roleAdmin->givePermissionTo([$permissionRolesCreate, $permissionRolesView, $permissionRolesUpdate, $permissionRolesDelete]);
        $roleAdmin->givePermissionTo([$permissionProfesorCreate,$permissionProfesorView,$permissionProfesorUpdate,$permissionProfesorDelete]);
        $roleAdmin->givePermissionTo([$permissionAlumnoCreate,$permissionAlumnoView,$permissionAlumnoUpdate,$permissionAlumnoDelete]);

        $roleProfesor->givePermissionTo([$permissionProfesorView]);
        $roleAlumno->givePermissionTo([$permissionAlumnoView]);
    
        // Asignar roles a usuarios
        $user = User::find(1);
        $user->assignRole($roleAdmin);

        $user = User::find(2);
        $user->assignRole($roleProfesor);

        $user = User::find(3);
        $user->assignRole($roleAlumno);
    }
}
