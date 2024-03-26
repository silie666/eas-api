<?php
declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
        \Package\Exceptions\Client\BadRequestException::class,
        \Package\Exceptions\Server\RuntimeNotLogException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable               $e
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e): \Symfony\Component\HttpFoundation\Response
    {
        if ($this->isRequestUsingApiResponse($request)) {
            return app(ApiHandler::class)->setDontReport($this->dontReport)->render($request, $e);
        }
        return parent::render($request, $e);
    }

    /**
     * 判断当前请求是否归属于Api请求
     *
     * @param \Illuminate\Http\Request|null $request
     *
     * @return bool
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function isRequestUsingApiResponse(\Illuminate\Http\Request $request = null): bool
    {
        if (!$request) {
            if (!$this->container || !$this->container->has('request')) {
                return false;
            }
            $request = $this->container->make('request');
        }

        if ($request::hasMacro('usingApiResponse')) {
            return $request->usingApiResponse();
        }

        return false;
    }
}
