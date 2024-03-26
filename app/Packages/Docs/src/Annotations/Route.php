<?php

namespace Package\ApiDocs\Annotations;

class Route
{
    /**
     * @var string
     */
    public string $controller;

    /**
     * @var array
     */
    public array $methods = [];

    /**
     * @var string
     */
    public string $uri;

    /**
     * @var string|null
     */
    public ?string $operationId = null;

    /**
     * @var array
     */
    public array $middlewares = [];

    /**
     * @var array
     */
    public array $tags = [];

    /**
     * @var string
     */
    public string $summary = '';

    /**
     * @var string|null
     */
    public ?string $description = '';

    /**
     * 参数列表
     *
     * @var \Package\ApiDocs\Annotations\RouteParameter[]
     */
    public array $parameters = [];

    /**
     * @var \Package\ApiDocs\Annotations\Response[]
     */
    public array $responses = [];
}
