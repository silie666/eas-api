<?php

namespace Package\Exceptions\Client;

/**
 * Class BadRequestException
 *
 * @package Package\Exceptions\Client
 */
class BadRequestException extends BaseException
{
    /**
     * @param string $message The internal exception message
     * @param \Throwable $previous The previous exception
     * @param int $code The internal exception code
     */
    public function __construct($message = '请求出错', \Throwable $previous = null, $code = 0)
    {
        parent::__construct(400, $message, $previous, [], $code);
    }
}
