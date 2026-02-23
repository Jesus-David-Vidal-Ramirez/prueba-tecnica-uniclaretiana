<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'guard_name',
    ];

    protected function name(): Attribute{
        return Attribute::make(
            get: fn ($value) => ucwords($value),
        );
    }

}
