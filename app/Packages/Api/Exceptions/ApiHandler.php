<?php

namespace Package\Api\Exceptions;

use Illuminate\Auth\Access\AuthorizationException as IlluminateAuthorizationException;
use Illuminate\Auth\AuthenticationException as IlluminateAuthenticationException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Package\Api\Http\ApiResponse;
use Package\Exceptions\BaseException as PackageBaseException;
use Package\Exceptions\Client as PackageClient;
use Package\Exceptions\Server as PackageServer;
use Throwable;

class ApiHandler implements ExceptionHandlerContract
{
    /**
     * The container implementation.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * A list of the exception types that are not reported.
     *
     * @var string[]
     */
    protected $dontReport = [];

    /**
     * A list of the internal exception types that should not be reported.
     *
     * @var array
     */
    protected $internalDontReport = [
        HttpException::class,
        HttpResponseException::class,
        IlluminateAuthenticationException::class,
        IlluminateAuthorizationException::class,
        IlluminateValidationException::class,
        ModelNotFoundException::class,
        TokenMismatchException::class,
        PackageClient\AuthenticationException::class,
        PackageClient\ValidationException::class,
        PackageClient\NotFoundException::class,
    ];

    /**
     * Create a new exception handler instance.
     *
     * @param \Illuminate\Contracts\Container\Container $container
     *
     * @return void
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Report or log an exception.
     *
     * @param \Throwable $e
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $e)
    {
        if ($this->shouldntReport($e)) {
            return;
        }

        try {
            $logger = $this->container->make(LoggerInterface::class);
        } catch (Throwable $ex) {
            throw $e; // throw the original exception
        }

        $logger->error(
            $e->getMessage(),
            ['exception' => $e]
        );
    }

    /**
     * 设置不需要记录的异常
     *
     * @param array $dontReport
     *
     * @return $this
     */
    public function setDontReport(array $dontReport)
    {
        $this->dontReport = $dontReport;
        return $this;
    }

    /**
     * Determine if the exception is in the "do not report" list.
     *
     * @param Throwable $e
     *
     * @return bool
     */
    protected function shouldntReport(Throwable $e)
    {
        $dontReport = array_merge($this->dontReport, $this->internalDontReport);

        return !is_null(Arr::first($dontReport, function ($type) use ($e) {
            return $e instanceof $type;
        }));
    }

    /**
     * Determine if the exception should be reported.
     *
     * @param \Throwable $e
     *
     * @return bool
     */
    public function shouldReport(Throwable $e)
    {
        return !$this->shouldntReport($e);
    }

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
    public function render($request, Throwable $e)
    {
        return $this->prepareResponse($this->prepareException($e));
    }

    /**
     * Render an exception to the console.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Throwable                                        $e
     *
     * @return void
     */
    public function renderForConsole($output, Throwable $e)
    {
        (new ConsoleApplication)->renderThrowable($e, $output);
    }

    /**
     * 异常转换成Api用的异常
     *
     * @param Throwable $e
     *
     * @return PackageBaseException
     */
    protected function prepareException(Throwable $e)
    {
        // 如果异常属于基础异常，直接返回
        if ($e instanceof PackageBaseException) {
            return $e;
        }

        if ($e instanceof ModelNotFoundException) {
            // 资源不存在
            try {
                $modelClass = $e->getModel();
                $modelName  = $modelClass::MODEL_NAME;
                $message    = ($modelName ?: '资源') . '不存在';
            } catch (Throwable $ex) {
                $message = '资源不存在';
            }
            $e = new PackageClient\NotFoundException($message, $e);
        } elseif ($e instanceof IlluminateValidationException) {
            // 表单验证失败
            $e = new PackageClient\ValidationException($e->validator, $e);
        } elseif ($e instanceof IlluminateAuthenticationException) {
            // 登录失败
            $e = new PackageClient\AuthenticationException('请先登录', $e);
        } elseif ($e instanceof IlluminateAuthorizationException || $e instanceof AccessDeniedHttpException) {
            // 已登录但权限不够
            $e = new PackageClient\ForbiddenException('权限不足', $e);
        } elseif ($e instanceof HttpException) {
            // 手动生成的HttpException，根据statusCode区别处理
            switch ($e->getStatusCode()) {
                case 429: // 接口访问频率限制
                    $e = new PackageClient\TooManyRequestsException('访问过于频繁', $e,
                        $e->getHeaders());
                    break;

                default:
                    break;

            }
        }

        return $e;
    }

    /**
     * 异常转成响应
     *
     * @param Throwable $e
     *
     * @return ApiResponse
     */
    protected function prepareResponse(Throwable $e)
    {
        if (!$e instanceof PackageBaseException) {
            if (\App::isLocal()) {
                $e = new PackageServer\InternalServerException($e->getMessage());
            } else {
                $e = new PackageServer\InternalServerException();
            }
        }

        $data = ['message' => $e->getMessage()];
        if ($e instanceof PackageClient\ValidationException) {
            $data['errors'] = $e->errors();
        }

        return new ApiResponse($data, $e->getStatusCode(), $e->getHeaders());
    }
}
