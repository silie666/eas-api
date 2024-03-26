<?php

namespace Package\Exceptions\Client;

/**
 * Class NotFoundException
 *
 * @package Package\Exceptions\Client
 */
class NotFoundException extends BaseException
{
    /**
     * @param string $message The internal exception message
     * @param \Throwable $previous The previous exception
     * @param int $code The internal exception code
     */
    public function __construct($message = '找不到资源', \Throwable $previous = null, $code = 0)
    {
        parent::__construct(404, $message, $previous, [], $code);
    }
}
