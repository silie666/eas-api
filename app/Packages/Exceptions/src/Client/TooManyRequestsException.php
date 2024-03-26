<?php

namespace Package\Exceptions\Client;

/**
 * Class TooManyRequestsException
 *
 * @package Package\Exceptions\Client
 */
class TooManyRequestsException extends BaseException
{
    /**
     * TooManyRequestsException constructor.
     *
     * @param string $message The internal exception message
     * @param \Throwable $previous The previous exception
     * @param array $headers An array of response headers
     * @param int $code The internal exception code
     */
    public function __construct($message = '访问过于频繁', \Throwable $previous = null, array $headers = [], $code = 0)
    {
        parent::__construct(429, $message, $previous, $headers, $code);
    }
}
