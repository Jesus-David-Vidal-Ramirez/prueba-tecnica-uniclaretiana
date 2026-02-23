<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{

    public $table ="roles";
    protected $primaryKey = 'id';
    public $timestamps = true;
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
