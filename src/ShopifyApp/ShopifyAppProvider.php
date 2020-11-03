<?php

namespace OhMyBrew\ShopifyApp;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use OhMyBrew\ShopifyApp\Console\WebhookJobMakeCommand;
use OhMyBrew\ShopifyApp\Middleware;
use OhMyBrew\ShopifyApp\Observers\ShopObserver;

/**
 * This package's provider for Laravel.
 */
class ShopifyAppProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Routes
        $this->loadRoutesFrom(__DIR__ . '/resources/routes.php');

        // Views
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'shopify-app');

        // Views publish
        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/vendor/shopify-app'),
        ], 'shopify-views');

        // Config publish
        $this->publishes([
            __DIR__ . '/resources/config/shopify-app.php' => "{$this->app->configPath()}/shopify-app.php",
        ], 'shopify-config');

        // Database migrations
        // @codeCoverageIgnoreStart
        if (Config::get('shopify-app.manual_migrations')) {
            $this->publishes([
                __DIR__ . '/resources/database/migrations' => "{$this->app->databasePath()}/migrations",
            ], 'shopify-migrations');
        } else {
            $this->loadMigrationsFrom(__DIR__ . '/resources/database/migrations');
        }
        // @codeCoverageIgnoreEnd

        // Job publish
        $this->publishes([
            __DIR__ . '/resources/jobs/AppUninstalledJob.php' => "{$this->app->path()}/Jobs/AppUninstalledJob.php",
        ], 'shopify-jobs');

        // Shop observer
        $shopModel = Config::get('shopify-app.shop_model');
        $shopModel::observe(ShopObserver::class);

        // Middlewares
        /**
         * @var \Illuminate\Routing\Router $router
         */
        $router = $this->app['router'];
        $router->aliasMiddleware('auth.webhook', Middleware\AuthWebhook::class);
        $router->aliasMiddleware('auth.proxy', Middleware\AuthProxy::class);

        $router->aliasMiddleware('billable', Middleware\Billable::class);
        $router->aliasMiddleware('billable.ajax', Middleware\BillableAjax::class);

        $router->aliasMiddleware('auth.shop', Middleware\AuthShop::class);
        $router->aliasMiddleware('auth.shop.ajax', Middleware\AuthShopAjax::class);
        $router->aliasMiddleware('auth.jwt.ajax', Middleware\AuthJwtAjax::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Merge options with published config
        $this->mergeConfigFrom(__DIR__ . '/resources/config/shopify-app.php', 'shopify-app');

        // ShopifyApp facade
        $this->app->bind('shopifyapp', function ($app) {
            return new ShopifyApp($app);
        });

        // Commands
        $this->commands([
            WebhookJobMakeCommand::class,
        ]);
    }
}
