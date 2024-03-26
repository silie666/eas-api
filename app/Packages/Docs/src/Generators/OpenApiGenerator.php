<?php

namespace Package\ApiDocs\Generators;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use OpenApi\Analysis;
use OpenApi\Annotations\ExternalDocumentation;
use OpenApi\Annotations\Info;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\MediaType;
use OpenApi\Annotations\OpenApi;
use OpenApi\Annotations\Parameter;
use OpenApi\Annotations\Property;
use OpenApi\Annotations\RequestBody;
use OpenApi\Annotations\Response;
use OpenApi\Annotations\Schema;
use OpenApi\Annotations\SecurityScheme;
use OpenApi\Context;
use OpenApi\Generator as OAGenerator;
use OpenApi\Loggers\DefaultLogger;
use Package\ApiDocs\Annotations\Route;
use RuntimeException;

/**
 * Class OpenApiGenerator
 *
 * @package Package\ApiDocs\Generators
 */
class OpenApiGenerator extends Generator
{
    /**
     * 资源定义数组
     *
     * @var \OpenApi\Annotations\Schema[]
     */
    protected array $oaSchemas = [];

    protected static array $removeSuffixes = ['Resource', 'Controller'];
    protected static array|null $removePrefixes = null;

    /**
     * 生成文件
     *
     * @return string
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     * @throws \RuntimeException|\Illuminate\Contracts\Container\BindingResolutionException
     */
    public function generate(): string
    {
        $openApiAnalysis = $this->createOpenApiAnalysisFromAnalyser();

        // Post-processing
        $openApiAnalysis->process((new OAGenerator())->getProcessors());
        // Validation (Generate notices & warnings)
        $openApiAnalysis->validate();

        return $openApiAnalysis->openapi->toJson();
    }

    /**
     * 生成文档文件
     *
     * @param string $name
     * @param string $path
     *
     * @return bool
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     * @throws \RuntimeException|\Illuminate\Contracts\Container\BindingResolutionException
     */
    public function build(string $name, string $path): bool
    {
        // 生成目录
        File::makeDirectory($path, 777, true, true);
        // 生成文件
        $result = file_put_contents($path . DIRECTORY_SEPARATOR . $name . '.json', $this->generate());

        return $result !== false;
    }

    /**
     * 创建OpenApiAnalysis
     *
     * @return \OpenApi\Analysis
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     * @throws \RuntimeException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createOpenApiAnalysisFromAnalyser(): Analysis
    {
        $rootContext = new Context([
            'version' => OpenApi::VERSION_3_1_0,
            'logger'  => new DefaultLogger(),
        ]);
        $analysis    = new Analysis([], $rootContext);

        // 添加项目信息
        $analysis->addAnnotation(new Info($this->option('info')), new Context([
            'filename' => __FILE__,
            'line'     => __LINE__,
        ], $rootContext));
        // 添加扩展文档信息
        if ($externalDocs = $this->option('external_docs')) {
            $analysis->annotations->attach(new ExternalDocumentation($externalDocs), new Context([
                'filename' => __FILE__,
                'line'     => __LINE__,
            ], $rootContext));
        }

        $annotationRoutes   = $this->analyser->getAnnotationRoutes();
        $openApiAnnotations = $this->transformToOpenApiAnnotations($annotationRoutes);
        foreach ($openApiAnnotations as $annotation) {
            $analysis->annotations->attach($annotation, new Context([
                'filename' => __FILE__,
                'line'     => __LINE__,
            ], $rootContext));
        }

        // 添加全局schema
        foreach ($this->oaSchemas as $schema) {
            $analysis->annotations->attach($schema, new Context([
                'filename' => __FILE__,
                'line'     => __LINE__,
            ], $rootContext));
        }

        return $analysis;
    }

    /**
     * 将注释转化成OpenApi的注释
     *
     * @param \Package\ApiDocs\Annotations\Route[] $annotationRoutes
     *
     * @return \OpenApi\Annotations\AbstractAnnotation[]
     * @throws \RuntimeException
     */
    protected function transformToOpenApiAnnotations(array $annotationRoutes): array
    {
        $annotations = [];
        foreach ($annotationRoutes as $annotationRoute) {
            foreach ($annotationRoute->methods as $method) {
                $method = strtolower($method);
                // 跳过head方法
                if ($method === 'head') {
                    continue;
                }

                $className = 'OpenApi\Annotations\\' . ucfirst($method);
                if (!class_exists($className)) {
                    // TODO: 打印错误
                    continue;
                }

                $security = null;
                foreach ($annotationRoute->middlewares as $middleware) {
                    if (str_starts_with($middleware, 'rbac')) {
                        $schema = new Schema(array_filter([
                            'type'   => 'string',
                            "format" => "string",
                        ]));

                        $security = new Parameter([
                            'name'        => 'Authorization',
                            'description' => '授权',
                            'in'          => 'header',
                            'value'       => $schema,
                            'example'     => 'Bearer {{token}}',
                        ]);
                        break;
                    }
                }
                if (in_array($method, ['post', 'put', 'patch'])) {
                    if (\Str::endsWith($annotationRoute->parameters[0]->name, ['UUID', 'ID', 'Id'])) {
                        array_shift($annotationRoute->parameters);
                    }
                    $values = collect($this->transformToOpenApiRequestBody($annotationRoute));
                } else {
                    $values = collect($this->transformToOpenApiParameters($annotationRoute->parameters));
                }

                if ($security) {
                    $values->push($security);
                }
                $values = $values->concat($this->transformToOpenApiResponses($annotationRoute->responses));

                $annotations[] = new $className([
                    'tags'        => $this->getRouteTags($annotationRoute),
                    'path'        => $annotationRoute->uri,
                    'operationId' => $this->getRouteOperationId($method, $annotationRoute),
                    'summary'     => $annotationRoute->summary,
                    'description' => (string)$annotationRoute->description,
                    'value'       => $values->toArray(),
                ]);
            }
        }

        return $annotations;
    }

    /**
     * 将参数转换成OpenApi注释
     *
     * @param \Package\ApiDocs\Annotations\RouteParameter[] $annotationParameters
     *
     * @return \OpenApi\Annotations\Parameter[]
     */
    protected function transformToOpenApiParameters(array $annotationParameters): array
    {
        $parameters = [];
        foreach ($annotationParameters as $annotationParameter) {

            $rule = $annotationParameter->rule;

            // 设置最大最小值
            $maximum = $minimum = $maxLength = $minLength = null;
            if ($rule->primitiveType === $rule::PRIMITIVE_TYPE_INTEGER) {
                $maximum = $rule->max;
                $minimum = $rule->min;
            } else {
                $maxLength = $rule->max;
                $minLength = $rule->min;
            }

            $schema = new Schema(array_filter([
                'type'      => $rule->primitiveType,
                'format'    => $rule->type,
                'nullable'  => $rule->nullable,
                'maximum'   => $maximum,
                'minimum'   => $minimum,
                'maxLength' => $maxLength,
                'minLength' => $minLength,
                'value'     => $rule->primitiveType === 'array' ? new Items([
                    'type' => 'string',
                ]) : null,
            ]));

            $parameters[] = new Parameter([
                'name'        => $annotationParameter->name,
                'in'          => $annotationParameter->in,
                'required'    => $rule->required,
                'description' => $annotationParameter->description,
                'value'       => $schema,
            ]);
        }

        return $parameters;
    }

    /**
     * 获取文档注释
     *
     * @param \Package\ApiDocs\Annotations\RouteParameter[] $annotationParameters
     *
     * @return \OpenApi\Annotations\Parameter[]
     */
    protected function properties(array $annotationParameters, int $len = 0)
    {
        $len        = $len ?: count($annotationParameters);
        $properties = [];

        for ($i = 0; $i < $len; $i++) {
            if (!isset($annotationParameters[$i])) {
                continue;
            }

            $annotationParameter = $annotationParameters[$i];
            $rule                = $annotationParameter->rule;

            // 设置最大最小值
            $maximum = $minimum = $maxLength = $minLength = null;
            if ($rule->primitiveType === $rule::PRIMITIVE_TYPE_INTEGER) {
                $maximum = $rule->max;
                $minimum = $rule->min;
            } else {
                $maxLength = $rule->max;
                $minLength = $rule->min;
            }

            if ($rule->primitiveType === 'array') {
                $isItem   = $isObject = true;
                $son      = $sonRequired = [];
                $itemType = $itemPrimitiveType = 'string';

                foreach ($annotationParameters as $k => $item) {
                    $key = $annotationParameter->name . '.';
                    if (str_starts_with($item->name, $key)) {
                        $sonRule = $item->rule;

                        if (str_starts_with($item->name, $key . '*.')) {
                            $isObject = false;
                        }

                        $item->name = substr($item->name, strlen($key));

                        if ($item->name === '*') {
                            $isItem            = false;
                            $itemType          = $item->rule->type;
                            $itemPrimitiveType = $item->rule->primitiveType;
                            unset($annotationParameters[$k]);
                            break;
                        }

                        if ($sonRule->required && !Str::contains($item->name, ['.', '*']) && $isObject) {
                            $sonRequired[] = $item->name;
                        }

                        unset($annotationParameters[$k]);
                        $son[] = $item;
                    }
                }

                if ($isItem) {
                    if ($isObject) {
                        $items        = $this->properties($son, count($son));
                        $properties[] = new Property([
                            'property'    => $annotationParameter->name,
                            'type'        => 'object',
                            'description' => $annotationParameter->description,
                            'value'       => $items,
                            'required'    => $sonRequired,
                        ]);
                    } else {
                        $sonItemRequired = [];

                        foreach ($son as $item) {
                            $sonRule = $item->rule;
                            if (str_starts_with($item->name, '*.')) {
                                $item->name = substr($item->name, 2);
                            }
                            if ($sonRule->required) {
                                $sonItemRequired[] = substr($item->name, 2);
                            }
                        }
                        $items = new Items([
                            'type'        => 'object',
                            'description' => $annotationParameter->description,
                            'properties'  => $this->properties($son, count($son)),
                            'required'    => $sonItemRequired,
                        ]);
                        if (str_starts_with($annotationParameter->name, '*.')) {
                            $annotationParameter->name = substr($annotationParameter->name, 2);
                        }
                        $properties[] = new Property([
                            'property'    => $annotationParameter->name,
                            'type'        => $rule->primitiveType,
                            'description' => $annotationParameter->description,
                            'value'       => $items,
                        ]);
                    }
                } else {
                    $properties[] = new Property([
                        'property'    => $annotationParameter->name,
                        'type'        => $rule->primitiveType,
                        'description' => $annotationParameter->description,
                        'value'       => new Items([
                            'type'   => $itemPrimitiveType,
                            'format' => $itemType,
                        ]),
                    ]);
                }
            } else {
                if (str_ends_with($annotationParameter->name, '.*')) {
                    $properties[] = new Property([
                        'type' => 'string',
                    ]);
                } elseif (str_starts_with($annotationParameter->name, '*.')) {
                    $name         = substr($annotationParameter->name, 2);
                    $properties[] = new Property([
                        'property'    => $name,
                        'type'        => $rule->primitiveType,
                        'format'      => $rule->type,
                        'nullable'    => $rule->nullable,
                        'maximum'     => $maximum,
                        'minimum'     => $minimum,
                        'maxLength'   => $maxLength,
                        'minLength'   => $minLength,
                        'description' => $annotationParameter->description,
                    ]);
                } else {
                    $properties[] = new Property([
                        'property'    => $annotationParameter->name,
                        'type'        => $rule->primitiveType,
                        'format'      => $rule->type,
                        'nullable'    => $rule->nullable,
                        'maximum'     => $maximum,
                        'minimum'     => $minimum,
                        'maxLength'   => $maxLength,
                        'minLength'   => $minLength,
                        'description' => $annotationParameter->description,
                    ]);
                }
            }
        }
        return $properties;
    }

    /**
     * 将参数转换成OpenApi注释
     *
     * @param \Package\ApiDocs\Annotations\Route $annotationRoute
     *
     * @return \OpenApi\Annotations\RequestBody[]
     */
    protected function transformToOpenApiRequestBody(Route $annotationRoute): array
    {

        // todo 特殊处理文件，图片上传接口，因为他们都不是application/json,而是multipart/form-data
        if (in_array($annotationRoute->uri, ['common-api/files', 'common-api/images'])) {
            $properties[]   = new Property([
                'property'    => 'files[]',
                'type'        => 'file',
                'description' => '图片',
            ]);
            $schemas        = new Schema([
                'type'       => 'object',
                'properties' => $properties,
            ]);
            $requestContent = new MediaType([
                'mediaType' => 'multipart/form-data',
                'schema'    => $schemas,
            ]);
        } else {
            // 填充必填
            $required = [];
            foreach ($annotationRoute->parameters as $item) {
                if (!Str::contains($item->name, ['.', '*']) && $item->rule->required) {
                    $required[] = $item->name;
                }
            }
            // 获取属性
            $parameters     = collect($this->properties($annotationRoute->parameters));
            $schemas        = new Schema([
                'type'       => 'object',
                'required'   => $required,
                'properties' => $parameters,
            ]);
            $requestContent = new MediaType([
                'mediaType' => 'application/json',
                'schema'    => $schemas,
            ]);
        }
        $body[] = new RequestBody(
            [
                'required' => true,
                'value'    => [
                    $requestContent,
                ],
            ]
        );
        return $body;
    }

    /**
     * 将响应转换成OpenApi注释
     *
     * @param \Package\ApiDocs\Annotations\Response[] $annotationResponses
     *
     * @return \OpenApi\Annotations\Response[]
     */
    protected function transformToOpenApiResponses(array $annotationResponses): array
    {
        $responses = [];

        foreach ($annotationResponses as $annotationResponse) {
            $oaSchema = $this->loadResponseClassAsSchema($annotationResponse->className);

            $oaResponseSchema    = null;
            $additionDescription = '';
            if ($annotationResponse->isCollection) {
                $additionDescription = '数组';
                $oaResponseSchema    = new Schema([
                    'type'  => 'array',
                    'value' => new Items([
                        'ref' => '#/components/schemas/' . $oaSchema->schema,
                    ]),
                ]);
            } else {
                $oaResponseSchema = new Schema([
                    'ref' => '#/components/schemas/' . $oaSchema->schema,
                ]);
            }

            $responses[] = new Response([
                'response'    => $annotationResponse->statusCode,
                'description' => $annotationResponse->description . $additionDescription,
                'value'       => new MediaType([
                    'mediaType' => $annotationResponse->mediaType,
                    'value'     => $oaResponseSchema,
                ]),
            ]);
        }

        return $responses;
    }

    /**
     * 将响应对应的类转换成Schema
     *
     * @param string $className
     *
     * @return \OpenApi\Annotations\Schema|null
     */
    protected function loadResponseClassAsSchema(string $className): ?Schema
    {
        $schemaName = $this->extraNameFromClass($className);
        if (!$schemaName) {
            return null;
        }

        if (isset($this->oaSchemas[$schemaName])) {
            return $this->oaSchemas[$schemaName];
        }

        // 读取properties
        $oaProperties        = [];
        $subSchemaClassNames = [];
        $properties          = $className::properties();
        foreach ($properties as $name => $property) {
            $type = Arr::get($property, 'type', 'string');
            if ($type === 'object' || $type === 'collection') {
                // 加载子Model
                $subSchemaClassNames[] = $property['ref'];
                $subSchemaName         = $this->extraNameFromClass($property['ref']);
                if ($type === 'collection') {
                    $oaProperties[] = new Property([
                        'property' => $name,
                        'type'     => 'array',
                        'value'    => new Items([
                            'ref' => '#/components/schemas/' . $subSchemaName,
                        ]),
                    ]);
                } else {
                    $oaProperties[] = new Property([
                        'property' => $name,
                        'ref'      => '#/components/schemas/' . $subSchemaName,
                    ]);
                }
            } else {
                // 移除对解析文档无帮助的key
                unset($property['key'], $property['mutator'], $property['divisor'], $property['precision']);
                // 添加当前属性名称
                $property['property'] = $name;
                // 数组额外处理格式
                if ($property['type'] === 'array') {
                    $property['value'] = new Items([
                        'type' => Arr::pull($property, 'format', 'string'),
                    ]);
                }

                $oaProperties[] = new Property($property);
            }
        }

        $this->oaSchemas[$schemaName] = $oaSchema = new Schema([
            'schema' => $schemaName,
            'type'   => 'object',
            'value'  => $oaProperties,
        ]);

        foreach ($subSchemaClassNames as $subSchemaClassName) {
            $this->loadResponseClassAsSchema($subSchemaClassName);
        }

        return $oaSchema;
    }

    /**
     * 抽取类名
     *
     * @param string $className
     *
     * @return string|null
     */
    protected function extraNameFromClass(string $className): ?string
    {
        if (!class_exists($className)) {
            return null;
        }

        if (is_null(static::$removePrefixes)) {
            $subNamespace = $this->option('namespace');
            if (!$subNamespace) {
                $subNamespace = collect(explode('/', $this->analyser->getUriPrefix()))
                    ->map(fn($string) => Str::studly($string))
                    ->implode('\\');
            }
            static::$removePrefixes = [
                "\App\Http\Resources\\$subNamespace\\",
                "\App\Http\Resources\\Common\\",
                "\App\Http\Controllers\\$subNamespace\\",
                "\App\Http\Controllers\\Common\\",
            ];
        }

        $className = Str::start($className, '\\');
        $result    = $this->removePrefixes($className, static::$removePrefixes);
        $result    = $this->removeSuffixes($result, static::$removeSuffixes);

        // 前缀移除失败，抛出异常
        if (str_starts_with($result, '\\')) {
            throw new RuntimeException('路由分组 ' . $this->analyser->getUriPrefix() .
                ' 下禁止使用 ' . $className . ' 作为类名，只能使用以下命名空间之一：' .
                implode('、', static::$removePrefixes));
        }

        return $this->formatName($result);
    }

    /**
     * 移除前缀
     *
     * @param string        $string
     * @param string[]|null $prefixes
     *
     * @return string
     */
    protected function removePrefixes(string $string, array|null $prefixes): string
    {
        foreach ($prefixes as $prefix) {
            if (str_starts_with($string, $prefix)) {
                return substr($string, strlen($prefix));
            }
        }
        return $string;
    }

    /**
     * 移除后缀
     *
     * @param string   $string
     * @param string[] $suffixes
     *
     * @return string
     */
    protected function removeSuffixes(string $string, array $suffixes): string
    {
        $stringLength = strlen($string);
        foreach ($suffixes as $suffix) {
            $position = $stringLength - strlen($suffix);
            if (strrpos($string, $suffix) === $position) {
                return substr($string, 0, $position);
            }
        }
        return $string;
    }

    /**
     * 获取路由标签列表
     *
     * @param \Package\ApiDocs\Annotations\Route $route
     *
     * @return string[]
     */
    protected function getRouteTags(Route $route): array
    {
        return [$this->extraNameFromClass($route->controller)];
    }

    /**
     * 获取路由OperationId
     *
     * @param string                             $method
     * @param \Package\ApiDocs\Annotations\Route $route
     *
     * @return string
     */
    protected function getRouteOperationId(string $method, Route $route): string
    {
        static $existsOperationIds = [];

        $params      = [];
        $operationId = Str::kebab($route->operationId);
        if (!$operationId) {
            $paths = array_filter(explode('/', $route->uri));
            foreach ($paths as $index => $path) {
                if (Str::startsWith($path, '{')) {
                    $prevIndex = $index - 1;
                    if ($prevIndex >= 0) {
                        // 存在参数，则将上一参数改为单数
                        $paths[$prevIndex] = Str::singular($paths[$prevIndex]);
                    }
                    $params[] = (string)Str::of($paths[$index])
                        ->substr(1, -1)
                        ->replace('UUID', 'Uuid')
                        ->kebab();
                    unset($paths[$index]);
                }
            }

            $operationId = strtolower($method) . '-' . implode('-', $paths);
        }

        if (isset($existsOperationIds[$operationId]) && $params) {
            $operationId .= '-by-' . implode('-and-', $params);
        }

        $existsOperationIds[$operationId] = true;

        return $operationId;
    }

    /**
     * 格式化返回名称
     *
     * @param string $name
     *
     * @return string
     */
    protected function formatName(string $name): string
    {
        return collect(explode('\\', $name))->map(function ($string) {
            return Str::kebab($string);
        })->implode('.');
    }
}
