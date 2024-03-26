<?php

namespace Package\Constant\Console;

use Illuminate\Console\Command;
use Package\Constant\ConstantCompiler;

class ConstantCacheCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'constant:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a cache file for faster constant loading';

    /**
     * The constant compiler instance.
     *
     * @var \Package\Constant\ConstantCompiler
     */
    protected $compiler;

    /**
     * @var string
     */
    protected $path;

    /**
     * Create a new config clear command instance.
     *
     * @param \Package\Constant\ConstantCompiler $compiler
     * @param string                            $path
     */
    public function __construct(ConstantCompiler $compiler, $path)
    {
        parent::__construct();

        $this->compiler = $compiler;
        $this->path     = $path;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->call('constant:clear');

        $this->compiler->compile($this->path);

        $this->info('Constant cached successfully!');
    }
}
