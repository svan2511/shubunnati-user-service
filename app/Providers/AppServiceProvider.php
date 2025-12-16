<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\AuthenticationException;

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
         // Handle unauthenticated exceptions globally
        app()->resolving(\Illuminate\Contracts\Debug\ExceptionHandler::class, function ($handler) {
            $handler->renderable(function (AuthenticationException $e, $request) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            });
        });
    }
}
