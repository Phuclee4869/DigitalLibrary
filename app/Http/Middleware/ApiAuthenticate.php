<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate;

class ApiAuthenticate extends Authenticate
{
    protected function redirectTo($request): ?string
    {
        return null;
    }
}