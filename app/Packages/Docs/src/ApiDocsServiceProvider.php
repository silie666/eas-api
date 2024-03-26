<?php

namespace Package\ApiDocs;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ApiDocsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Bootstrap the application api client.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('api-docs', function ($app) {
            return new Factory($app);
        });

        $this->app->singleton('command.api-docs.generate', function () {
            return new Commands\GenerateDocs();
        });

        $this->commands([
            'command.api-docs.generate',
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [
            'api-docs',
            'command.api-docs.generate',
        ];
    }

    /**
     * Get the config path
     *
     * @return string
     */
    protected function getConfigPath(): string
    {
        return config_path('api-docs.php');
    }
}
