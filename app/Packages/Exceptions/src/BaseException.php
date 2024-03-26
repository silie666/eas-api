<?php

namespace Package\Exceptions;

/**
 * Class BaseException
 *
 * @package Package\Exceptions
 */
class BaseException extends \RuntimeException
{
    private $statusCode;
    private $headers;

    public function __construct(
        $statusCode,
        $message = null,
        \Throwable $previous = null,
        array $headers = [],
        $code = 0
    ) {
        $this->statusCode = $statusCode;
        $this->headers    = $headers;

        parent::__construct($message, $code, $previous);
    }

    /**
     * 获取状态码
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * 附加headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set response headers.
     *
     * @param array $headers Response headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }
}
