<?php

namespace Package\Exceptions\Client;

/**
 * Class ForbiddenException
 *
 * @package Package\Exceptions\Client
 */
class ForbiddenException extends BaseException
{
    /**
     * @param string $message The internal exception message
     * @param \Throwable $previous The previous exception
     * @param int $code The internal exception code
     */
    public function __construct($message = '权限不足', \Throwable $previous = null, $code = 0)
    {
        parent::__construct(403, $message, $previous, [], $code);
    }
}
