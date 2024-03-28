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
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
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
    protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

//        $this->routes(function () {
        $this->mapApiRoutes();
//
        $this->mapWebRoutes();
//
        $this->mapDocsRoutes();
//        });


    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }


    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->prefix('api')
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        // 教师端接口
        Route::prefix('api/teacher-api')
            ->middleware('api')
            ->group(base_path('routes/teacher-api.php'));

        // 学生端接口
        Route::prefix('api/student-api')
            ->middleware('api')
            ->group(base_path('routes/student-api.php'));

        // 公共调用接口
        Route::prefix('api/common-api')
            ->middleware('api')
            ->group(base_path('routes/common-api.php'));
    }


    /**
     * Define the "docs" routes for the application.
     *
     * @return void
     */
    protected function mapDocsRoutes()
    {
        if ($this->app->make('config')->get('app.env') !== 'production') {
            Route::prefix('api/docs')->group(base_path('routes/docs.php'));
        }
    }
}
