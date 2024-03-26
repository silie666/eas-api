<?php

namespace Package\ApiDocs;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Package\ApiDocs\Analysers\LaravelAnalyser;
use Package\ApiDocs\Generators\Generator;
use Package\ApiDocs\Generators\OpenApiGenerator;

class Factory
{
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected Application $app;

    /**
     * @var string
     */
    protected string $buildPath;

    /**
     * @var array
     */
    protected array $globalOptions;

    /**
     * @var array
     */
    protected array $providers;

    /**
     * @var array
     */
    protected array $generators = [];

    /**
     * Factory constructor.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct(Application $app)
    {
        $this->app = $app;

        $config              = $this->app->make('config')->get('api-docs', []);
        $this->buildPath     = Arr::get($config, 'build_path');
        $this->globalOptions = Arr::get($config, 'global_options', []);
        $this->providers     = Arr::get($config, 'providers', []);
    }

    /**
     * 根据名称生成文档
     *
     * @param string $name
     *
     * @return string
     */
    public function generate(string $name): string
    {
        return $this->resolve($name)->generate();
    }

    /**
     * 将文档写入路径
     *
     * @param string $name
     *
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function build(string $name): bool
    {
        return $this->resolve($name)->build($name, $this->buildPath);
    }

    /**
     * 解析generator
     *
     * @param string $name
     *
     * @return \Package\ApiDocs\Generators\Generator
     * @throws \InvalidArgumentException
     */
    protected function resolve(string $name): Generator
    {
        if (!isset($this->providers[$name])) {
            throw new InvalidArgumentException('Provider name [' . $name . '] does not exists.');
        }

        if (isset($this->generators[$name])) {
            return $this->generators[$name];
        }

        $provider = $this->providers[$name];
        $options  = array_merge_recursive($this->globalOptions, Arr::get($provider, 'options', []));

        $analyser  = new LaravelAnalyser($this->app, ['uri' => Arr::get($provider, 'uri', $name)]);
        $generator = new OpenApiGenerator($this->app, $analyser, $options);

        return $this->generators[$name] = $generator;
    }

}
