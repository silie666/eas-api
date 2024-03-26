<?php

namespace Package\ApiDocs\Analysers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Object_;
use ReflectionClass;
use ReflectionMethod;
use Package\ApiDocs\Annotations\Response as AnnotationResponse;
use Package\ApiDocs\Annotations\Route as AnnotationRoute;
use Package\ApiDocs\Annotations\RouteParameter as AnnotationRouteParameter;
use Package\ApiDocs\Annotations\RouteParameterRule as AnnotationRouteParameterRule;

abstract class Analyser
{

    /**
     * Laravel Application
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected Application $app;

    /**
     * DocBlockFactory
     *
     * @var \phpDocumentor\Reflection\DocBlockFactory
     */
    protected DocBlockFactory $docBlockFactory;

    /**
     * @var string
     */
    protected string $uriPrefix;

    /**
     *
     * Analyser constructor.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param array                                        $options
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(Application $app, array $options)
    {
        $this->app             = $app;
        $this->docBlockFactory = DocBlockFactory::createInstance();
        $this->uriPrefix       = trim(Arr::get($options, 'uri'), '/');

        if (empty($this->uriPrefix)) {
            throw new InvalidArgumentException('Config should set uri.');
        }
    }

    /**
     * 获取URI前缀
     *
     * @return string
     */
    public function getUriPrefix(): string
    {
        return $this->uriPrefix;
    }

    /**
     * 将当前路由列表转化成可读对象
     *
     * @return array
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getAnnotationRoutes(): array
    {
        $annotationRoutes = [];

        $routes = $this->app->make('router')->getRoutes();
        foreach ($routes as $route) {
            // 只解析匹配url的路由
            /* @var \Illuminate\Routing\Route $route */
            if (!Str::startsWith($route->uri(), $this->uriPrefix)) {
                continue;
            }

            if(Str::contains($route->uri(),'{fallbackPlaceholder}') ){
                continue;
            }

            // 跳过空调用和可执行函数
            $routeHandler = $route->getAction()['uses'];
            if (empty($routeHandler) || is_callable($routeHandler)) {
                continue;
            }

            $annotationRoutes[] = $this->transformRouteToAnnotation($route);
        }

        return $annotationRoutes;
    }

    /**
     * 将路由转化成已解析的对象
     *
     * @param \Illuminate\Routing\Route $route
     *
     * @return \Package\ApiDocs\Annotations\Route
     * @throws \ReflectionException|\Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function transformRouteToAnnotation(Route $route): AnnotationRoute
    {
        $annotationRoute = new AnnotationRoute();
        // 添加请求方法与url
        $annotationRoute->methods     = $route->methods();
        $annotationRoute->uri         = $route->uri();
        $annotationRoute->middlewares = $route->gatherMiddleware();

        // 反射执行方法
        $routeAction = $route->getAction();
        [$controllerClass, $method] = explode('@', $routeAction['uses']);
        $reflectionClass  = new ReflectionClass($controllerClass);

        $reflectionMethod = $reflectionClass->getMethod($method);

        $docBlock = $this->docBlockFactory->create($reflectionMethod->getDocComment());

        // 添加概要与详情
        $annotationRoute->summary     = $docBlock->getSummary();
        $annotationRoute->description = $docBlock->getDescription();

        // 添加操作ID
        /* @var \phpDocumentor\Reflection\DocBlock\Tags\Generic|null $operationId */
        $operationId = Arr::first($docBlock->getTagsByName('operationId'));
        if ($operationId) {
            $annotationRoute->operationId = $operationId->getDescription()->getBodyTemplate();
        }

        // 设置控制器class
        $annotationRoute->controller = $controllerClass;

        // 添加请求参数
        $annotationRoute->parameters = $this->analyseRouteParameters($reflectionMethod, $route);

        // 添加响应参数
        $annotationRoute->responses = $this->analysisRouteResponses($reflectionMethod);

        return $annotationRoute;
    }

    /**
     * 分析路由参数
     *
     * @param \ReflectionMethod         $reflectionMethod
     * @param \Illuminate\Routing\Route $route
     *
     * @return \Package\ApiDocs\Annotations\RouteParameter[]
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function analyseRouteParameters(ReflectionMethod $reflectionMethod, Route $route): array
    {
        $rules      = [];
        $attributes = [];
        $method     = $route->methods()[0];

        foreach ($reflectionMethod->getParameters() as $parameter) {
            $parameterType = $parameter->getType();
            if ($parameterType && class_exists($parameterType->getName()) &&
                is_subclass_of($parameterType->getName(), FormRequest::class)) {
                /* @var FormRequest $request */
                $className = $parameterType->getName();
                $request   = new $className();
                $request->setContainer($this->app);

                if (method_exists($request, 'validator')) {
                    $validator  = $this->app->call([$request, 'validator']);
                    $rules      = $validator->getRules();
                    $attributes = $validator->getAttributes();
                } elseif (method_exists($request, 'rules') &&
                    method_exists($request, 'attributes')) {
                    $rules      = $this->app->call([$request, 'rules']);
                    $attributes = $this->app->call([$request, 'attributes']);
                }
                break;
            }
        }

        // 触发compliedRoute方法
        $route->matches(request(), false);

        // URL参数
        $parameters    = [];
        $compiledRoute = $route->getCompiled();
        $docBlock      = $this->docBlockFactory->create($reflectionMethod->getDocComment());
        $methodParams  = collect($docBlock->getTagsByName('param'))->mapWithKeys(function (Param $param) {
            return [$param->getVariableName() => $param];
        });

        // 解析路由参数
        foreach ($compiledRoute->getPathVariables() as $variable) {
            $routeParameter = new AnnotationRouteParameter();
            /* @var \phpDocumentor\Reflection\DocBlock\Tags\Param $methodParam */
            $methodParam = $methodParams->get($variable, new Param($variable));

            $ruleType = (string)$methodParam->getType();
            if ($ruleType === 'float' || $ruleType === 'double') {
                $ruleType = 'numeric';
            } elseif ($ruleType !== 'int' && $ruleType !== 'bool') {
                $ruleType = 'string';
            }

            $routeParameter->name        = $variable;
            $routeParameter->method      = $method;
            $routeParameter->in          = AnnotationRouteParameter::IN_PATH;
            $routeParameter->rule        = new AnnotationRouteParameterRule(['required', $ruleType]);
            $routeParameter->description = (string)$methodParam->getDescription();

            $parameters[] = $routeParameter;
        }
        if (!$rules) {
            return $parameters;
        }

        foreach ($rules as $name => $rule) {
            $routeParameter = new AnnotationRouteParameter();

            $routeParameter->name = $name;
            $routeParameter->in   = AnnotationRouteParameter::IN_QUERY;
            if (is_string($rule)) {
                $rule = explode('|', $rule);
            }
            $routeParameter->rule        = new AnnotationRouteParameterRule($rule);
            $routeParameter->description = Arr::get($attributes, $name, '');

            $parameters[] = $routeParameter;
        }

        return $parameters;
    }

    /**
     * 分析路由响应
     *
     * @param \ReflectionMethod $reflectionMethod
     *
     * @return \Package\ApiDocs\Annotations\Response[]
     * @throws \InvalidArgumentException
     */
    protected function analysisRouteResponses(ReflectionMethod $reflectionMethod): array
    {
        $docBlock = $this->docBlockFactory->create($reflectionMethod->getDocComment());
        // 获取@return注释
        $returnTag = head($docBlock->getTagsByName('return'));
        if (!$returnTag instanceof Return_) {
            throw new \RuntimeException('获取返回类型失败：' . $reflectionMethod->getFileName() . '::' . $reflectionMethod->getName());
        }

        $returnTagType = $returnTag->getType();

        // 存在多个返回类型，默认解析第一个
        if ($returnTagType instanceof Compound) {
            $returnTagType = $returnTagType->get(0);
        }

        // 处理数组
        $isCollection = false;
        if ($returnTagType instanceof Array_) {
            $isCollection  = true;
            $returnTagType = $returnTagType->getValueType();
        }

        // 检查类型是否符合
        if (!$returnTagType instanceof Object_) {
            throw new \RuntimeException('返回信息必须为完整类名：' . $reflectionMethod->getFileName() . '::' . $reflectionMethod->getName());
        }

        // 检查类名是否存在，检查类是否存在 schema 方法
        $className = (string)$returnTagType->getFqsen();
        if (!class_exists($className) && method_exists($className, 'schema')) {
            throw new \RuntimeException($className . ' 类不存在或类不存在 schema 方法');
        }

        // 开始生成响应
        $schema     = $className::schema();
        $properties = $className::properties();
        $response   = AnnotationResponse::createJson();

        $response->isCollection = $isCollection;
        $response->className    = $className;
        $response->statusCode   = Arr::get($schema, 'statusCode', 200);
        $response->description  = Arr::get($schema, 'description');
        $response->properties   = $properties;

        return [$response];
    }
}
