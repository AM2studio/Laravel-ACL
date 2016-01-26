<?php

namespace AM2Studio\LaravelACL;

use Illuminate\Support\ServiceProvider;

class LaravelACLServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path(
                'migrations'
            )
        ], 'migrations');

        $this->mergeConfigFrom(
            __DIR__.'/../config/acl.php',
            'acl'
        );

        $this->publishes([
            __DIR__.'/../config/acl.php' => config_path(
                'acl.php'
            )
        ], 'config');

        $this->registerBladeExtensions();
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {

    }

    private function registerBladeExtensions()
    {
        $blade = $this->app['view']->getEngineResolver()->resolve('blade')->getCompiler();

        $blade->directive('role', function ($expression) {
            return "<?php if (Auth::check() && Auth::user()->is{$expression}): ?>";
        });

        $blade->directive('endrole', function () {
            return "<?php endif; ?>";
        });

        $blade->directive('permission', function ($expression) {
            return "<?php if (Auth::check() && Auth::user()->can{$expression}): ?>";
        });

        $blade->directive('endpermission', function () {
            return "<?php endif; ?>";
        });

        $blade->directive('allowed', function ($expression) {
            return "<?php if (Auth::check() && Auth::user()->allowed{$expression}): ?>";
        });

        $blade->directive('endallowed', function () {
            return "<?php endif; ?>";
        });
    }
}
