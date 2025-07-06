<?php

namespace App\Models;

use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;
use Spatie\Multitenancy\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant
{
    use UsesLandlordConnection;

    protected $fillable = ['name', 'slug', 'database'];

    public function databaseName(): string
    {
        return $this->database;
    }
}
