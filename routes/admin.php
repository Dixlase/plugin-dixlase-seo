<?php

/**
 * This file is part of Dixlase SEO.
 *
 * Copyright (C) 2026 exc-D inc.
 * https://exc-d.com
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

use Illuminate\Support\Facades\Route;
use Plugins\DixlaseSEO\App\Http\Controllers\Admin\CrawlerSettingsController;
use Plugins\DixlaseSEO\App\Http\Controllers\Admin\ExternalSettingsController;
use Plugins\DixlaseSEO\App\Http\Controllers\Admin\IntegrationsController;
use Plugins\DixlaseSEO\App\Http\Controllers\Admin\MetaSettingsController;

/*
|--------------------------------------------------------------------------
| DixlaseSEO Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('dixlase-seo')
    ->name('dixlase-seo::admin.dixlase-seo.')
    ->group(function () {
        // メタタグ・OGP設定
        Route::get('meta', [MetaSettingsController::class, 'show'])->name('meta');
        Route::patch('meta', [MetaSettingsController::class, 'update'])->name('meta.update');

        // サイトマップ・robots.txt設定
        Route::get('crawler', [CrawlerSettingsController::class, 'show'])->name('crawler');
        Route::patch('crawler', [CrawlerSettingsController::class, 'update'])->name('crawler.update');

        // 外部サービス連携設定
        Route::get('external', [ExternalSettingsController::class, 'show'])->name('external');
        Route::patch('external', [ExternalSettingsController::class, 'update'])->name('external.update');

        // プラグイン連携設定
        Route::get('integrations', [IntegrationsController::class, 'show'])->name('integrations');
    });
