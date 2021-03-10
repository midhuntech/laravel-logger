<?php

namespace midhuntech\LaravelLogger;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use midhuntech\LaravelLogger\App\Http\Middleware\LogActivity;
use Illuminate\Support\Facades\Config;

class LaravelLoggerServiceProvider extends ServiceProvider
{
    const DISABLE_DEFAULT_ROUTES_CONFIG = 'laravel-logger.disableRoutes';

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * The event listener mappings for the applications auth scafolding.
     *
     * @var array
     */
    protected $listeners = [

        'Illuminate\Auth\Events\Attempting' => [
            'midhuntech\LaravelLogger\App\Listeners\LogAuthenticationAttempt',
        ],

        'Illuminate\Auth\Events\Authenticated' => [
            'midhuntech\LaravelLogger\App\Listeners\LogAuthenticated',
        ],

        'Illuminate\Auth\Events\Login' => [
            'midhuntech\LaravelLogger\App\Listeners\LogSuccessfulLogin',
        ],

        'Illuminate\Auth\Events\Failed' => [
            'midhuntech\LaravelLogger\App\Listeners\LogFailedLogin',
        ],

        'Illuminate\Auth\Events\Logout' => [
            'midhuntech\LaravelLogger\App\Listeners\LogSuccessfulLogout',
        ],

        'Illuminate\Auth\Events\Lockout' => [
            'midhuntech\LaravelLogger\App\Listeners\LogLockout',
        ],

        'Illuminate\Auth\Events\PasswordReset' => [
            'midhuntech\LaravelLogger\App\Listeners\LogPasswordReset',
        ],

    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $router->middlewareGroup('activity', [LogActivity::class]);
        $this->loadTranslationsFrom(__DIR__.'/resources/lang/', 'LaravelLogger');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        if (file_exists(Config::get('view.compiled'))) {
            $this->mergeConfigFrom(config_path('laravel-logger.php'), 'LaravelLogger');
        } else {
            $this->mergeConfigFrom(__DIR__.'/config/laravel-logger.php', 'LaravelLogger');
        }

        if (config(self::DISABLE_DEFAULT_ROUTES_CONFIG) == false) {
            $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        }

        $this->loadViewsFrom(__DIR__.'/resources/views/', 'LaravelLogger');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $this->registerEventListeners();
        $this->publishFiles();
    }

    /**
     * Get the list of listeners and events.
     *
     * @return array
     */
    private function getListeners()
    {
        return $this->listeners;
    }

    /**
     * Register the list of listeners and events.
     *
     * @return void
     */
    private function registerEventListeners()
    {
        $listeners = $this->getListeners();
        foreach ($listeners as $listenerKey => $listenerValues) {
            foreach ($listenerValues as $listenerValue) {
                Event::listen(
                    $listenerKey,
                    $listenerValue
                );
            }
        }
    }

    /**
     * Publish files for Laravel Logger.
     *
     * @return void
     */
    private function publishFiles()
    {
        $publishTag = 'LaravelLogger';

        $this->publishes([
            __DIR__.'/config/laravel-logger.php' => base_path('config/laravel-logger.php'),
        ], $publishTag);

        $this->publishes([
            __DIR__.'/resources/views' => base_path('resources/views/vendor/'.$publishTag),
        ], $publishTag);

        $this->publishes([
            __DIR__.'/resources/lang' => base_path('resources/lang/vendor/'.$publishTag),
        ], $publishTag);
    }
}
