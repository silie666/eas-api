<?php

namespace Package\Constant\Console;

use Illuminate\Console\Command;
use Illuminate\View\Engines\PhpEngine;

/**
 * A command to generate constant meta data
 *
 */
class ConstantMetaCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'constant:meta';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate constant metadata for PhpStorm';

    /**
     * @var \Package\Constant\ConstantCompiler
     */
    protected $compiler;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $view;

    /**
     * @var string
     */
    protected $compiledPath;

    /**
     * Metadata methods.
     *
     * @var array
     */
    protected $keyMethods = [
        '\Package\Constant\Constant::has(0)',
        '\Package\Constant\Constant::get(0)',
        '\Package\Constant\Constant::lang(0)',
        '\cons(0)',
    ];

    protected $valueMethods = [
        '\Package\Constant\Constant::hasValue(0)',
        '\Package\Constant\Constant::key(0)',
        '\Package\Constant\Constant::valueLang(0)',
    ];

    /**
     *
     * @param \Package\Constant\ConstantCompiler     $compiler
     * @param \Illuminate\Contracts\Filesystem\Filesystem $files
     * @param string                                      $compiledPath
     */
    public function __construct($compiler, $files, $compiledPath)
    {
        $this->compiler     = $compiler;
        $this->files        = $files;
        $this->compiledPath = $compiledPath;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Make constant cache update to date.
        $this->call('constant:cache');

        // Format render data.
        ['key' => $keyConstants, 'value' => $valueIndexConstants] = require $this->compiler->getCompiledPath();

        $constantKeys = [];
        foreach ($keyConstants as $key => $value) {
            if (\Arr::has($value, 'zh-CN')) {
                $constantKeys[$key] = "中文名：{$value['zh-CN']}，返回值：{$value['_value']}";
            } else {
                $constantKeys[$key] = \stdClass::class;
            }
        }

        $constantValueKeys = collect($valueIndexConstants)->keys()->map(function ($key) {
            return substr($key, 0, strrpos($key, '.'));
        })->unique();


        $content = $this->renderMetaFile([
            'keyMethods'        => $this->keyMethods,
            'valueMethods'      => $this->valueMethods,
            'constantKeys'      => $constantKeys,
            'constantValueKeys' => $constantValueKeys,
        ]);

        $written = $this->files->put($this->compiledPath, $content);
        $this->files->chmod($this->compiledPath, 0777);


        if ($written !== false) {
            $this->info("A new meta file was written to {$this->compiledPath}");
        } else {
            $this->error("The meta file could not be created at {$this->compiledPath}");
        }
    }

    /**
     * Render constant meta content.
     *
     * @param array $data
     *
     * @return string
     */
    protected function renderMetaFile($data = [])
    {
        return app(PhpEngine::class)->get(__DIR__ . '/../../resources/views/meta.php', $data);
    }
}
