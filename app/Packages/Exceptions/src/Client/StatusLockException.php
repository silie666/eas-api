<?php

namespace Package\Exceptions\Client;

/**
 * 状态锁异常
 *
 * Class StatusLockException
 *
 * @package Package\Exceptions\Client
 */
class StatusLockException extends BaseException
{
    /**
     * 状态锁的异常原因通常属于并发请求或系统维护等原因造成，所以状态码选择 503
     *
     * StatusLockException constructor.
     *
     * @param string          $message
     * @param \Exception|null $previous
     */
    public function __construct(string $message = '状态锁更新失败', \Exception $previous = null)
    {
        parent::__construct(503, $message, $previous, [], 5001);
    }
}
