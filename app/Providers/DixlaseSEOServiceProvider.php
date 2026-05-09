<?php

/**
 * This file is part of Dixlase SEO.
 *
 * Copyright (C) 2026 exc-D inc.
 * https://exc-d.com
 *
 * Dixlase SEO is dual-licensed. You may use this file under either:
 *
 *   (a) the GNU General Public License version 3 or later, as published
 *       by the Free Software Foundation; or
 *
 *   (b) a commercial license agreement obtained from exc-D inc.
 *
 * Unless you have entered into a commercial license agreement, this
 * file is governed by the GPL terms below.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace Plugins\DixlaseSEO\App\Providers;

use App\Contracts\CspPolicyProvider;
use App\Contracts\PluginIntegration\SeoMetaProviderInterface;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Plugins\DixlaseSEO\App\Http\Middleware\InjectSeoMetaTags;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;
use Plugins\DixlaseSEO\App\Services\DixlaseSeoMetaProvider;
use Plugins\DixlaseSEO\App\Services\JsonLdGenerator;
use Plugins\DixlaseSEO\App\Services\SeoMetaGenerator;
use Plugins\DixlaseSEO\App\Services\SitemapGenerator;

/**
 * ServiceProvider for DixlaseSEO plugin
 */
class DixlaseSEOServiceProvider extends ServiceProvider implements CspPolicyProvider
{
    /**
     * Register services
     */
    public function register(): void
    {
        // Merge settings file
        $this->mergeConfigFrom(
            __DIR__.'/../../config/dixlase_seo.php',
            'dixlase_seo'
        );

        // Register service as singleton
        $this->app->singleton(SeoMetaGenerator::class);
        $this->app->singleton(JsonLdGenerator::class);

        // Register Contract implementation for per-content SEO meta information
        // Other plugins can resolve via app(SeoMetaProviderInterface::class)
        $this->app->singleton(SeoMetaProviderInterface::class, DixlaseSeoMetaProvider::class);

        // Inject LinkableProvider into SitemapGenerator
        // Note: $app->tagged() returns an iterator regardless of whether tags are registered,
        // bound() check is unnecessary (bound() always returns false as it checks bindings, not tags)
        $this->app->singleton(SitemapGenerator::class, function ($app) {
            $providers = iterator_to_array($app->tagged('linkable.providers'));

            return new SitemapGenerator($providers);
        });
    }

    /**
     * Bootstrap services
     */
    public function boot(): void
    {
        // Register CSP policy
        $this->registerCspPolicy();

        // Register views
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'dixlase-seo');

        // Register anonymous Blade components (available from other plugins as <x-dixlase-seo::name />)
        Blade::anonymousComponentPath(__DIR__.'/../../resources/views/components', 'dixlase-seo');

        // Register translation files
        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'dixlase-seo');

        // Register migrations
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // Register middleware
        $this->registerMiddleware();

        // Note: routes (routes/web.php, routes/admin.php) are auto-loaded by PluginServiceProvider

        // Public assets
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/dixlase_seo.php' => config_path('dixlase_seo.php'),
            ], 'dixlase-seo-config');

            $this->publishes([
                __DIR__.'/../../resources/views' => resource_path('views/vendor/dixlase-seo'),
            ], 'dixlase-seo-views');
        }
    }

    /**
     * Register CSP policy
     */
    protected function registerCspPolicy(): void
    {
        if (app()->bound(\App\Services\Csp\CspPolicyRegistry::class)) {
            app(\App\Services\Csp\CspPolicyRegistry::class)
                ->registerProvider('dixlase-seo', $this);
        }
    }

    /**
     * Retrieve CSP directives
     *
     * @return array<string, array<string>>
     */
    public function getCspDirectives(): array
    {
        $gaId = DixlaseSeoSetting::getValue('google_analytics_id', config('dixlase_seo.google_analytics_id', ''));

        if (! $gaId) {
            return [];
        }

        return [
            'script-src' => [
                'https://www.googletagmanager.com',
                'https://www.google-analytics.com',
            ],
            'connect-src' => [
                'https://www.google-analytics.com',
                'https://*.google-analytics.com',
                'https://*.analytics.google.com',
            ],
            'img-src' => [
                'https://www.google-analytics.com',
                'https://www.googletagmanager.com',
            ],
        ];
    }

    /**
     * Register middleware
     */
    protected function registerMiddleware(): void
    {
        /** @var Router $router */
        $router = $this->app->make(Router::class);

        // Add SEO meta tag injection middleware to web group (runs on all front pages)
        $router->pushMiddlewareToGroup('web', InjectSeoMetaTags::class);
    }
}
