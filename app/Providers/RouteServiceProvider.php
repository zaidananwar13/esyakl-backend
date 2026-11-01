<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    // public function boot()
    // {
    //     $this->configureRateLimiting();

    //     $this->routes(function () {
    //         Route::prefix('api')
    //             ->middleware('api')
    //             ->namespace($this->namespace)
    //             ->group(base_path('routes/api.php'));

    //         Route::middleware('web')
    //             ->namespace($this->namespace)
    //             ->group(base_path('routes/web.php'));
    //     });
    // }
    protected $namespace = 'App\\Http\\Controllers';
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('web')
                ->middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));

            Route::middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        // Default API rate limiter
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });

        // SECURITY FIX: Strict rate limiting for authentication endpoints
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip())->response(function () {
                return response()->json([
                    'title' => config('app-settings.api.title_prefix', 'E - Syakl') . ' | Auth API',
                    'code' => 429,
                    'message' => 'Too many login attempts. Please try again later.',
                ], 429);
            });
        });

        // Rate limiting for registration
        RateLimiter::for('registration', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip())->response(function () {
                return response()->json([
                    'title' => config('app-settings.api.title_prefix', 'E - Syakl') . ' | Auth API',
                    'code' => 429,
                    'message' => 'Too many registration attempts. Please try again later.',
                ], 429);
            });
        });

        // Rate limiting for password reset
        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip())->response(function () {
                return response()->json([
                    'title' => config('app-settings.api.title_prefix', 'E - Syakl') . ' | Profile API',
                    'code' => 429,
                    'message' => 'Too many password reset attempts. Please try again later.',
                ], 429);
            });
        });

        // Rate limiting for quiz submissions
        RateLimiter::for('quiz-submit', function (Request $request) {
            return Limit::perMinute(10)->by(
                optional($request->user())->id ?: $request->ip()
            );
        });
    }
}
