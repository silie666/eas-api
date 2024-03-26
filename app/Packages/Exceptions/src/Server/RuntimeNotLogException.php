<?php

namespace Package\Exceptions\Server;

/**
 * 运行时错误的异常（不记录日志）
 */
class RuntimeNotLogException extends BaseException
{
    /**
     * 运行时错误
     *
     * @param string          $message
     * @param \Exception|null $previous
     */
    public function __construct(string $message = '运行时错误', \Exception $previous = null)
    {
        parent::__construct(400, $message, $previous, [], 1001);
    }
}
