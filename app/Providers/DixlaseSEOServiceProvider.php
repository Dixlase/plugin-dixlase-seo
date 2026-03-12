<?php

/**
 * This file is part of DixlaseSEO.
 *
 * Copyright (C) 2026
 * https://example.com
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
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Plugins\DixlaseSEO\App\Http\Middleware\InjectSeoMetaTags;
use Plugins\DixlaseSEO\App\Services\SeoMetaGenerator;
use Plugins\DixlaseSEO\App\Services\JsonLdGenerator;
use Plugins\DixlaseSEO\App\Services\SitemapGenerator;

/**
 * DixlaseSEOプラグインのServiceProvider
 */
class DixlaseSEOServiceProvider extends ServiceProvider implements CspPolicyProvider
{
    /**
     * サービスを登録する
     */
    public function register(): void
    {
        // 設定ファイルをマージ
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/dixlase_seo.php',
            'dixlase_seo'
        );

        // サービスをシングルトンで登録
        $this->app->singleton(SeoMetaGenerator::class);
        $this->app->singleton(JsonLdGenerator::class);

        // SitemapGeneratorにLinkableProviderを注入
        $this->app->singleton(SitemapGenerator::class, function ($app) {
            $providers = [];
            if ($app->bound('linkable.providers')) {
                $providers = iterator_to_array($app->tagged('linkable.providers'));
            }

            return new SitemapGenerator($providers);
        });
    }

    /**
     * サービスを起動する
     */
    public function boot(): void
    {
        // CSPポリシーの登録
        $this->registerCspPolicy();

        // ビューの登録
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'dixlase-seo');

        // 翻訳ファイルの登録
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'dixlase-seo');

        // マイグレーションの登録
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        // ミドルウェアの登録
        $this->registerMiddleware();

        // ルートの登録
        $this->registerRoutes();

        // 公開可能なアセット
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/dixlase_seo.php' => config_path('dixlase_seo.php'),
            ], 'dixlase-seo-config');

            $this->publishes([
                __DIR__ . '/../../resources/views' => resource_path('views/vendor/dixlase-seo'),
            ], 'dixlase-seo-views');
        }
    }

    /**
     * CSPポリシーを登録する
     */
    protected function registerCspPolicy(): void
    {
        if (app()->bound(\App\Services\Csp\CspPolicyRegistry::class)) {
            app(\App\Services\Csp\CspPolicyRegistry::class)
                ->registerProvider('dixlase-seo', $this);
        }
    }

    /**
     * CSPディレクティブを取得する
     *
     * @return array<string, array<string>>
     */
    public function getCspDirectives(): array
    {
        return [];
    }

    /**
     * ミドルウェアを登録する
     */
    protected function registerMiddleware(): void
    {
        /** @var Router $router */
        $router = $this->app->make(Router::class);

        // SEOメタタグ注入ミドルウェアをwebグループに追加（全フロントページで動作）
        $router->pushMiddlewareToGroup('web', InjectSeoMetaTags::class);
    }

    /**
     * ルートを登録する
     */
    protected function registerRoutes(): void
    {
        // フロントエンドルート
        if (file_exists(__DIR__ . '/../../routes/web.php')) {
            Route::middleware(['plugin.web'])
                ->group(__DIR__ . '/../../routes/web.php');
        }

        // 管理画面ルート
        if (file_exists(__DIR__ . '/../../routes/admin.php')) {
            Route::middleware(['plugin.admin'])
                ->prefix(config('admin.url.admin_url', 'admin'))
                ->name('admin.')
                ->group(__DIR__ . '/../../routes/admin.php');
        }
    }
}
