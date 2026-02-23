<?php

namespace App\Http\Controllers\Administration;

use App\DTO\AlertDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\profesor\profesorStoreRequest;
use App\Http\Requests\Administration\profesor\profesorUpdateRequest;
use App\Models\Profesor;
use Inertia\Inertia;
use Illuminate\Routing\Controllers\HasMiddleware;

class ProfesorController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $alert = session('alert') ?? AlertDTO::success(
            'Profesores listados',
            'Profesores cargados exitosamente'
        )->toArray();

        return Inertia::render('profesor/Profesores', [
            'profesores' => Profesor::latest('is_active')->latest('created_at')->get(),
            'alert' => $alert,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(profesorStoreRequest $request)
    {
        Profesor::create($request->validated());

        $alert = AlertDTO::success('Profesor creado', 'Profesor creado exitosamente');

        return redirect()->route('profesor.index')->with('alert', $alert->toArray());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(profesorUpdateRequest $request, string $id)
    {
        $profesor = Profesor::findOrFail($id);
        $profesor->update($request->validated());

        $alert = AlertDTO::success('Profesor editado', 'Profesor editado exitosamente');

        return redirect()->route('profesor.index')->with('alert', $alert->toArray());
    }

    /**
     * disable or enable the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $profesor = Profesor::findOrFail($id);
        $message = $profesor->is_active ? 'Desactivado' : 'Activado';

        $profesor->update([
            'is_active' => ! $profesor->is_active,
        ]);

        $alert = AlertDTO::success("Profesor $message", "Profesor $message exitosamente");

        return redirect()->route('profesor.index')->with('alert', $alert->toArray());
    }
}
