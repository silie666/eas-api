<?php

namespace Package\ApiDocs\Generators;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use Package\ApiDocs\Analysers\Analyser;

abstract class Generator
{
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected Application $app;

    /**
     * @var \Package\ApiDocs\Analysers\Analyser
     */
    protected Analyser $analyser;

    /**
     * @var array
     */
    protected array $options;

    /**
     * Generator constructor.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param \Package\ApiDocs\Analysers\Analyser           $analyser
     * @param array                                        $options
     */
    public function __construct(Application $app, Analyser $analyser, array $options)
    {
        $this->app      = $app;
        $this->analyser = $analyser;
        $this->options  = $options;
    }

    /**
     * 生成文档
     *
     * @return string
     */
    abstract public function generate(): string;

    /**
     * 生成文件
     *
     * @param string $name
     * @param string $path
     *
     * @return bool
     */
    abstract public function build(string $name, string $path): bool;

    /**
     * 获取配置信息
     *
     * @param string     $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    protected function option(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->options, $key, $default);
    }
}
