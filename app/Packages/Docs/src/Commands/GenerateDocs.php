<?php

namespace Package\ApiDocs\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Package\ApiDocs\Analysers\LaravelAnalyser;
use Package\ApiDocs\Generators\OpenApiGenerator;

class GenerateDocs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-docs:generate
                                    {name : The provider name for the generated documentation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate your API documentation from existing Laravel routes.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Illuminate\Contracts\Container\CircularDependencyException
     * @throws \ReflectionException
     */
    public function handle(): int
    {
        $this->laravel->make('api-docs')->build();

        $config = $this->laravel->make('config')->get('api-docs');

        foreach (Arr::get($config, 'providers', []) as $provider) {

            $options = Arr::pull($provider, 'options', []);

            $analyser  = new LaravelAnalyser($this->laravel, ['uri' => Arr::get($provider, 'uri')]);
            $generator = new OpenApiGenerator($this->laravel, $analyser, $options);

            $generator->generate();
        }

        return 0;
    }
}
