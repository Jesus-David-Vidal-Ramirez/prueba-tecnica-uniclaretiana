<?php

namespace App\Http\Controllers\Administration;

use App\DTO\AlertDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\rol\rolStoreRequest;
use App\Http\Requests\Administration\rol\rolUpdateRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;


class RoleController extends Controller implements HasMiddleware
{

    public function __construct(){}

    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:view roles', only: ['index']),
            new Middleware('permission:create roles', only: ['store','create']),
            new Middleware('permission:update roles', only: ['edit','update','show']),
            new Middleware('permission:delete roles', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /**
         * el alert enviado desde store() o del metodo de donde venga
         */
        $alert = session('alert') ??  AlertDTO::success(
            'Roles listados',
            'Roles cargados exitosamente'
        )->toArray();

        return Inertia::render('administracion/rol/Rol', [
            'roles' => Role::latest()->get(),
            'alert' => $alert,
            'permisos' => session('permisos') ?? [],
            'rol' => session('rol') ?? [],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(rolStoreRequest $request)
    {
       
        Role::create($request->validated() );

        $alert = AlertDTO::success( 'Rol creado', 'Rol creado exitosamente');

        return redirect()->route('rol.index')->with('alert', $alert->toArray());
    
    }

    /**
     * Display the specified resource.
     * Display permisos assoccia the rol
     */
    public function show(Role $rol)
    {
        // $user = User::find(1);

        // $permissions = $user->getAllPermissions();
        $permissions = Permission::all()
        ->map(fn ($permission) => [
            'id' => $permission->id,
            'name' => $permission->name,
            'checked' => $rol->permissions->contains($permission->id),
        ]);

        return redirect()->route('rol.index')->with([
            'permisos' => $permissions,
            'rol' => $rol,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(rolUpdateRequest $request, Role $rol)
    {
        /**
         * actualizar el name
         */
        $rol->update($request->validated());

        $alert = AlertDTO::success( 'Rol actualizado', 'Rol actualizado exitosamente');

        return redirect()->route('rol.index')->with('alert', $alert->toArray());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $rol)
    {
        $rol->delete();

        $alert = AlertDTO::success( 'Rol eliminado', 'Rol eliminado exitosamente');

        return redirect()->route('rol.index')->with('alert', $alert->toArray());
    }

    /**
     * Summary of syncPermissions
     * @param Request $request
     * @param Role $rol
     * @return \Illuminate\Http\RedirectResponse
     */
    public function syncPermissions(Request $request, Role $rol)
    {
        $rol->syncPermissions($request->permisos);
    
        $alert = AlertDTO::success( 'permisos actualizados', 'Permisos Actualizados exitosamente');

        return back()->withErrors([])->with('alert', $alert->toArray());
    }

}
