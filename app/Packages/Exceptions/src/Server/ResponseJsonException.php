<?php

namespace Package\Exceptions\Server;

/**
 * Class ResponseJsonException
 * 服务器响应成功，但返回的内容json解析失败
 *
 * @package Package\Exceptions\Server
 */
class ResponseJsonException extends BaseException
{
    /**
     * 原始响应内容
     *
     * @var string|null
     */
    protected $content;

    /**
     * ServiceUnavailableException constructor.
     *
     * @param string          $message
     * @param string|null     $content
     * @param \Exception|null $previous
     * @param int             $code
     */
    public function __construct($message = '服务响应异常', $content = null, \Throwable $previous = null, $code = 0)
    {
        parent::__construct(503, $message, $previous, [], $code);

        $this->content = $content;
    }


    /**
     * 获取响应内容
     *
     * @return string|null
     */
    public function getContent()
    {
        return $this->content;
    }
}
