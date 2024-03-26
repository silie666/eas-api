<?php

namespace Package\Api;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Package\Api\Http\Middleware\UnifiedResponse;
use Package\Exceptions\Client\BadRequestException;

class ApiServiceProvider extends BaseServiceProvider
{

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'silie.api' => \Package\Api\Http\Middleware\UnifiedResponse::class,
    ];

    /**
     * Bootstrap the application Package api.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerMiddlewares();
    }

    protected function registerMiddlewares()
    {
        $router = $this->app['router'];
        foreach ($this->routeMiddleware as $key => $middleware) {
            $router->aliasMiddleware($key, $middleware);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // API用方法扩展
        $this->extendFoundation();
    }

    /**
     * 框架功能扩展
     */
    protected function extendFoundation()
    {
        \Request::macro('usingApiResponse', function () {
            static $usingApiResponse = null;

            if (is_null($usingApiResponse)) {
                // 默认没有使用
                $usingApiResponse = false;

                if ($route = $this->route()) {
                    // 解析middleware，判断是否存在UnifiedResponse这个Middleware
                    foreach (app('router')->gatherRouteMiddleware($route) as $middleware) {
                        [$class] = explode(':', $middleware);

                        if (UnifiedResponse::class === $class) {
                            $usingApiResponse = true;
                            break;
                        }
                    }
                }
            }

            return $usingApiResponse;
        });

        // 增加针对api的fallback
        \Route::macro('apiFallback', function () {
            /* @var \Illuminate\Routing\Router $router */
            $router      = $this;
            $placeholder = 'fallbackPlaceholder';
            return $router->any('{' . $placeholder . '}', function () {
                $request = app('router')->getCurrentRequest();
                // 记录请求的URL
                \Log::warning('请求接口不存在: ' . implode(' ', [
                        $request->method(),
                        $request->path(),
                        json_encode($request->input()),
                    ]));
                // 抛出异常
                throw new BadRequestException('请求接口不存在');
            })->where($placeholder, '.*')->fallback();
        });
    }
}
