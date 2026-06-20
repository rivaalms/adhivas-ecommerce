<?php

namespace App\Providers;

use App\Enums\UserRoleEnum;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Token;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        Auth::extend('jwt_cookie', function ($app, $name, array $config) {
            return new \Illuminate\Auth\RequestGuard(function ($request) use ($config) {
                $token = $request->cookie('access-token');
                if (!$token) return null;

                try {
                    Auth::setToken($token);
                    return Auth::user();
                } catch (\Throwable $e) {
                    throw $e;
                }
            }, $app['request'], $app['auth']->createUserProvider($config['provider']));
        });

        Gate::define('role:admin', function (User $user) {
            return $user->role === UserRoleEnum::ADMIN;
        });

        Gate::define('role:customer', function (User $user) {
            return $user->role === UserRoleEnum::CUSTOMER;
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(
            fn(): ?Password => app()->isProduction()
                ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
                : null,
        );
    }
}
