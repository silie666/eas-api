<?php

namespace Package\ApiDocs\Annotations;

class Response
{
    const MEDIA_TYPE_JSON = 'application/json';

    /**
     * 对应响应的名称
     *
     * @var string
     */
    public string $className;

    /**
     * 状态码
     *
     * @var int
     */
    public int $statusCode;

    /**
     * 描述
     *
     * @var string
     */
    public string $description = '';

    /**
     * 附带头部
     *
     * @var array
     */
    public array $headers = [];

    /**
     * 响应类型
     *
     * @var string
     */
    public string $mediaType;

    /**
     * 是否数组
     *
     * @var bool
     */
    public bool $isCollection = false;

    /**
     * 响应属性
     *
     * @var array
     */
    public array $properties = [];

    /**
     * 创建JSON响应
     *
     * @return \Package\ApiDocs\Annotations\Response
     */
    static function createJson(): Response
    {
        $response = new static();

        $response->mediaType = static::MEDIA_TYPE_JSON;

        return $response;
    }
}
