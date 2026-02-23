<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profesor extends Model
{
    use HasFactory;
    protected $table = "profesores";
    protected $fillable = [
        'nombre',
        'cedula',
        'grados_asignados',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected function isActiveText(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => (int) $attributes['is_active'] === 1 ? 'Activo' : 'Inactivo',
        );
    }
}
