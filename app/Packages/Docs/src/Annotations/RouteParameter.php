<?php

namespace Package\ApiDocs\Annotations;

class RouteParameter
{
    // 参数所在位置
    const IN_QUERY = 'query';
    const IN_HEADER = 'header';
    const IN_PATH = 'path';
    const IN_COOKIES = 'cookie';

    /**
     * 参数名
     *
     * @var string
     */
    public string $name;

    /**
     * 参数验证规则
     *
     * @var \Package\ApiDocs\Annotations\RouteParameterRule|null
     */
    public ?RouteParameterRule $rule;

    /**
     * 参数所在位置
     * "query", "header", "path" or "cookie"
     *
     * @var string
     */
    public string $in;

    /**
     * 参数描述列表
     *
     * @var string
     */
    public string $description = '';
}
