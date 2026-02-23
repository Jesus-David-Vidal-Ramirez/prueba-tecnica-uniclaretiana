<?php

use App\Http\Controllers\Administration\PermissionController;
use App\Http\Controllers\Administration\RoleController;
use App\Http\Controllers\Administration\UserController;
use App\Http\Controllers\Administration\AlumnoController;
use App\Http\Controllers\Administration\ProfesorController;
use App\Models\Alumno;
use App\Models\Profesor;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;


Route::fallback(fn () => Redirect::route('dashboard') );
 
Route::get('/', function () {
    if (auth()->check()) {
        return Redirect::route('dashboard');
    }

    return Inertia::render('auth/Login', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');


Route::middleware(['auth', 'verified', ])->group(function () {
    Route::get('dashboard', function () {
        $user = auth()->user();
        $roles = $user->getRoleNames()->map(fn (string $role) => strtolower(trim($role)));

        $isAdmin = $roles->contains(fn (string $role) => in_array($role, ['admin', 'administrador'], true));
        $isProfesor = $roles->contains('profesor');
        $isAlumno = $roles->contains('alumno');

        $currentRole = $isAdmin ? 'admin' : ($isProfesor ? 'profesor' : ($isAlumno ? 'alumno' : 'otro'));

        $adminUsers = User::whereHas('roles', fn ($query) => $query->whereIn('name', ['admin', 'administrador']))->count();
        $profesorUsers = User::whereHas('roles', fn ($query) => $query->where('name', 'profesor'))->count();
        $alumnoUsers = User::whereHas('roles', fn ($query) => $query->where('name', 'alumno'))->count();

        $normalizeText = static fn (?string $value): string => Str::of((string) $value)->lower()->ascii()->trim()->toString();

        $isApprovedStatus = static function (?string $status) use ($normalizeText): bool {
            $normalizedStatus = $normalizeText($status);

            return str_contains($normalizedStatus, 'aprob')
                || str_contains($normalizedStatus, 'gano')
                || str_contains($normalizedStatus, 'finalizada')
                || str_contains($normalizedStatus, 'passed')
                || str_contains($normalizedStatus, 'pass');
        };

        $isFailedStatus = static function (?string $status) use ($normalizeText): bool {
            $normalizedStatus = $normalizeText($status);

            return str_contains($normalizedStatus, 'desaprob')
                || str_contains($normalizedStatus, 'reprob')
                || str_contains($normalizedStatus, 'perdio')
                || str_contains($normalizedStatus, 'failed')
                || str_contains($normalizedStatus, 'fail');
        };

        $alumnosActivos = Alumno::query()
            ->where('is_active', true)
            ->get(['grado', 'estado_prueba']);

        $profesoresActivos = Profesor::query()
            ->where('is_active', true)
            ->get(['grados_asignados']);

        $gradosAsignados = $profesoresActivos
            ->flatMap(function (Profesor $profesor) use ($normalizeText) {
                return collect(preg_split('/[,;]+/', (string) $profesor->grados_asignados))
                    ->map(fn ($grade) => $normalizeText((string) $grade))
                    ->filter();
            })
            ->unique()
            ->values();

        $alumnosAsignados = $alumnosActivos
            ->filter(fn (Alumno $alumno) => $gradosAsignados->contains($normalizeText((string) $alumno->grado)))
            ->count();

        $resultadosPorGrupo = $alumnosActivos
            ->groupBy(fn (Alumno $alumno) => $normalizeText((string) $alumno->grado))
            ->map(function ($groupAlumnos) use ($isApprovedStatus, $isFailedStatus) {
                $label = trim((string) optional($groupAlumnos->first())->grado);
                $groupLabel = $label !== '' ? $label : 'Sin grado';
                $total = $groupAlumnos->count();
                $ganaron = $groupAlumnos->filter(fn (Alumno $alumno) => $isApprovedStatus($alumno->estado_prueba))->count();
                $desaprobaron = $groupAlumnos->filter(fn (Alumno $alumno) => $isFailedStatus($alumno->estado_prueba))->count();
                $porcentajeAprobacion = $total > 0 ? round(($ganaron / $total) * 100, 1) : 0;

                return [
                    'group' => $groupLabel,
                    'total' => $total,
                    'ganaron' => $ganaron,
                    'desaprobaron' => $desaprobaron,
                    'porcentaje_aprobacion' => $porcentajeAprobacion,
                ];
            })
            ->values();

        $ganadoresPorGrupo = $resultadosPorGrupo
            ->sortByDesc('ganaron')
            ->values();

        $mejoresGrados = $resultadosPorGrupo
            ->filter(fn (array $item) => $item['total'] > 0)
            ->sort(function (array $a, array $b) {
                $orderByRate = $b['porcentaje_aprobacion'] <=> $a['porcentaje_aprobacion'];

                if ($orderByRate !== 0) {
                    return $orderByRate;
                }

                return $b['ganaron'] <=> $a['ganaron'];
            })
            ->take(5)
            ->values();

        $totalGanaron = $alumnosActivos
            ->filter(fn (Alumno $alumno) => $isApprovedStatus($alumno->estado_prueba))
            ->count();

        $totalDesaprobaron = $alumnosActivos
            ->filter(fn (Alumno $alumno) => $isFailedStatus($alumno->estado_prueba))
            ->count();

        $totalPendientes = max($alumnosActivos->count() - $totalGanaron - $totalDesaprobaron, 0);

        return Inertia::render('Dashboard', [
            'currentRole' => $currentRole,
            'stats' => [
                'total_usuarios' => User::count(),
                'usuarios_admin' => $adminUsers,
                'usuarios_profesor' => $profesorUsers,
                'usuarios_alumno' => $alumnoUsers,
                'total_alumnos' => Alumno::count(),
                'total_profesores' => Profesor::count(),
            ],
            'analytics' => [
                'profesor' => [
                    'alumnos_asignados' => $alumnosAsignados,
                    'ganadores_por_grupo' => $ganadoresPorGrupo,
                    'mejores_grados' => $mejoresGrados,
                ],
                'alumno' => [
                    'ganaron' => $totalGanaron,
                    'desaprobaron' => $totalDesaprobaron,
                    'pendientes' => $totalPendientes,
                ],
            ],
        ]);
    })->name('dashboard');

    Route::resource('user', UserController::class)->names('user')->only(['index','store','update','destroy']);
    Route::resource('alumno', AlumnoController::class)->names('alumno')->only(['index','store','update','destroy']);
    Route::resource('profesor', ProfesorController::class)->names('profesor')->only(['index','store','update','destroy']);
    Route::resource('rol', RoleController::class)->names('rol')->only(['index','store','show','update','destroy']);
    Route::post('rol/{rol}/permissions', [RoleController::class, 'syncPermissions'])->name('rol.permissions.sync');
    Route::resource('permission', PermissionController::class)->names('permission')->only(['index','store','update','destroy']);

});

// Route::get('dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
