<?php

namespace App\Http\Controllers\Administration;

use App\DTO\AlertDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\user\userStoreRequest;
use App\Http\Requests\Administration\user\userUpdateRequest;
use App\Models\User;
use App\Models\Role;
use Inertia\Inertia;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    public function __construct() {}

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:view user', only: ['index']),
            new Middleware('permission:create user', only: ['store', 'create']),
            new Middleware('permission:update user', only: ['edit', 'update', 'show']),
            new Middleware('permission:delete user', only: ['destroy']),
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
            'Usuarios listados',
            'Usuarios cargados exitosamente'
        )->toArray();

        return Inertia::render('administracion/user/Users',  [
            'users' => User::with(['roles'])->latest('is_active')->get(),
            'roles' => Role::all(),
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
    public function store(userStoreRequest $request)
    {
        /**
         * Buscar el email no se encuentre registrado
         * */
        $roles = $request['rol'];
        $userCreated = User::create($request->validated());
        /**
         * asignar los roles que envia
         */
        $userCreated->assignRole([$roles]);

        $alert = AlertDTO::success('Usuario creado', 'Usuario creado exitosamente');

        return redirect()->route('user.index')->with('alert', $alert->toArray());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(userUpdateRequest $request, string $id)
    {
        /**
         * buscar si el correo pertenece al id ó si existe y es de otra persona
         */
        $name = $request['name'];
        $roles = $request['rol'];
        $email = $request['email'];

        /**
         * actualizar el nombre & email
         */
        $user = User::find($id);
        $user->update([
            'name' => $name,
            'email' => $email,
        ]);

        /**
         * sincronizarle los roles que envia
         */
        $user->syncRoles([$roles]);

        $alert = AlertDTO::success('Usuario editado', 'Usuario editado exitosamente');

        return redirect()->route('user.index')->with('alert', $alert->toArray());
    }

    /**
     * disable or enable the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        $message = $user->is_active == 1 ? 'Desactivado' : 'Activado';

        $user->update([
            'is_active' => $user->is_active == 1 ? 0 : 1,
        ]);

        $alert = AlertDTO::success("Usuario $message", "Usuario $message exitosamente");

        return redirect()->route('user.index')->with('alert', $alert->toArray());
    }
}
