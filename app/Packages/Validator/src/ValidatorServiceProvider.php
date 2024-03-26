<?php

namespace Package\Validator;

use Illuminate\Support\ServiceProvider;

class ValidatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application Package validator.
     *
     * @return void
     */
    public function boot()
    {
        // add validator extend translations
        $langPath = __DIR__ . '/../resources/lang';
        $this->loadTranslationsFrom($langPath, 'Package-validator');
        $this->publishes([$langPath => resource_path('lang/vendor/' . 'Package-validator')]);

        // add validator extend rules
        ValidateRuleExtend::addTo($this->app->make('validator'));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    }
}
