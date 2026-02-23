<?php

namespace App\Http\Controllers\Administration;

use App\DTO\AlertDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\alumno\alumnoStoreRequest;
use App\Http\Requests\Administration\alumno\alumnoUpdateRequest;
use App\Models\Alumno;
use Inertia\Inertia;
use Illuminate\Routing\Controllers\HasMiddleware;

class AlumnoController extends Controller implements HasMiddleware
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
            'Alumnos listados',
            'Alumnos cargados exitosamente'
        )->toArray();

        return Inertia::render('alumno/Alumnos', [
            'alumnos' => Alumno::latest('is_active')->latest('created_at')->get(),
            'alert' => $alert,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(alumnoStoreRequest $request)
    {
        Alumno::create($request->validated());

        $alert = AlertDTO::success('Alumno creado', 'Alumno creado exitosamente');

        return redirect()->route('alumno.index')->with('alert', $alert->toArray());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(alumnoUpdateRequest $request, string $id)
    {
        $alumno = Alumno::findOrFail($id);
        $alumno->update($request->validated());

        $alert = AlertDTO::success('Alumno editado', 'Alumno editado exitosamente');

        return redirect()->route('alumno.index')->with('alert', $alert->toArray());
    }

    /**
     * disable or enable the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $alumno = Alumno::findOrFail($id);
        $message = $alumno->is_active ? 'Desactivado' : 'Activado';

        $alumno->update([
            'is_active' => ! $alumno->is_active,
        ]);

        $alert = AlertDTO::success("Alumno $message", "Alumno $message exitosamente");

        return redirect()->route('alumno.index')->with('alert', $alert->toArray());
    }
}
