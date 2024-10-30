<?php

declare(strict_types=1);

namespace MoonShine;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

final class MoonShineAuth
{
    public static function model(): ?Model
    {
        $model = self::provider()?->getModel();

        return $model ? new $model() : null;
    }

    public static function provider(): ?UserProvider
    {
        return self::guard()->getProvider();
    }

    public static function guard(?string $guard = null): Guard|StatefulGuard
    {
        return Auth::guard($guard ?? self::guardName());
    }

    public static function guardName(): string
    {
        return config('moonshine.auth.guard', 'web');
    }
}
