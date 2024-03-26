<?php

namespace Package\Exceptions\Client;

/**
 * Class AuthenticationException
 * 认证异常，用于客户端和服务端之间的通信校验
 *
 * @package Package\Exceptions\Client
 */
class AuthenticationException extends BaseException
{
    /**
     * AuthorisationException constructor.
     *
     * @param string $message
     * @param \Exception|null $previous
     * @param int $code
     */
    public function __construct($message = '客户端尚未登录', \Throwable $previous = null, $code = 0)
    {
        parent::__construct(401, $message, $previous, [], $code);
    }
}
