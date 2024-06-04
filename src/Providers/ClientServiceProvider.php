<?php

namespace Spinen\QuickBooks\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Spinen\QuickBooks\Client;

/**
 * Class ClientServiceProvider
 */
class ClientServiceProvider extends LaravelServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     */
    protected bool $defer = true;

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [Client::class];
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->app->bind(Client::class, function (Application $app) {
            $config = $this->app->config->get('quickbooks.user');

            $token =
                $app->auth->guard($config['guard'])->user()->quickBooksToken ?:
                $app->auth
                    ->guard($config['guard'])
                    ->user()
                    ->quickBooksToken()
                    ->make();

            return new Client($app->config->get('quickbooks'), $token);
        });

        $this->app->alias(Client::class, 'QuickBooks');
    }
}
