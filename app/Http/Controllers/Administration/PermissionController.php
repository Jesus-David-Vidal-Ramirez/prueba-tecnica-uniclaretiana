<?php

namespace App\Http\Controllers\Administration;

use App\DTO\AlertDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\permission\permissionStoreRequest;
use App\Http\Requests\Administration\permission\permissionUpdateRequest;
use App\Models\Permission;
use Inertia\Inertia;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PermissionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:view permission', only: ['index']),
            new Middleware('permission:create permission', only: ['store', 'create']),
            new Middleware('permission:update permission', only: ['edit', 'update', 'show']),
            new Middleware('permission:delete permission', only: ['destroy']),
            // new Middleware('subscribed', except: ['store']),
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
        $alert = session('alert') ?? AlertDTO::success(
            'Permisos listados',
            'Permisos cargados exitosamente'
        )->toArray();
        $permisos = Permission::latest()->get();

        return Inertia::render('administracion/permission/Permission', [
            'permissions' => $permisos,
            'alert' => $alert,
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
    public function store(permissionStoreRequest $request)
    {
        Permission::create($request->validated());

        $alert = AlertDTO::success('Permiso creado', 'Permiso creado exitosamente');

        return redirect()->route('permission.index')->with('alert', $alert->toArray());
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(permissionUpdateRequest $request, Permission $permission)
    {
        /**
         * actualizar el name
         */
        $permission->update($request->validated());

        $alert = AlertDTO::success('Permiso actualizado', 'Permiso actualizado exitosamente');

        return redirect()->route('permission.index')->with('alert', $alert->toArray());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();

        $alert = AlertDTO::success('Permiso eliminado', 'Permiso eliminado exitosamente');

        return redirect()->route('permission.index')->with('alert', $alert->toArray());
    }
}
