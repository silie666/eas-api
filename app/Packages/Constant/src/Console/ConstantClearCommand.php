<?php

namespace Package\Constant\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ConstantClearCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'constant:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the constant cache file';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @var string
     */
    protected $compiledPath;

    /**
     * Create a new config clear command instance.
     *
     * @param \Illuminate\Filesystem\Filesystem $files
     * @param string                            $compiledPath
     *
     * @return void
     */
    public function __construct(Filesystem $files, $compiledPath)
    {
        parent::__construct();

        $this->files        = $files;
        $this->compiledPath = $compiledPath;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->files->delete($this->compiledPath);

        $this->info('Constant cache cleared!');
    }
}
