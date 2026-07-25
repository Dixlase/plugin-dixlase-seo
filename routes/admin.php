<?php

/**
 * This file is part of Dixlase SEO.
 *
 * Copyright (C) 2026 exc-D inc. and Dixlase contributors
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

use Illuminate\Support\Facades\Route;
use Plugins\DixlaseSEO\App\Http\Controllers\Admin\BaseSettingsController;
use Plugins\DixlaseSEO\App\Http\Controllers\Admin\ExternalSettingsController;
use Plugins\DixlaseSEO\App\Http\Controllers\Admin\IntegrationsController;
use Plugins\DixlaseSEO\App\Http\Controllers\Admin\SitemapSettingsController;

/*
|--------------------------------------------------------------------------
| DixlaseSEO Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('seo')
    ->name('dixlase-seo::admin.seo.')
    ->group(function () {
        // Basic settings (meta tags, OGP, organization info)
        Route::get('base', [BaseSettingsController::class, 'show'])->name('base');
        Route::patch('base', [BaseSettingsController::class, 'update'])->name('base.update');

        // Sitemap and robots.txt settings
        Route::get('sitemap', [SitemapSettingsController::class, 'show'])->name('sitemap');
        Route::patch('sitemap', [SitemapSettingsController::class, 'update'])->name('sitemap.update');

        // External service integration settings
        Route::get('external', [ExternalSettingsController::class, 'show'])->name('external');
        Route::patch('external', [ExternalSettingsController::class, 'update'])->name('external.update');

        // Plugin integration settings
        Route::get('integrations', [IntegrationsController::class, 'show'])->name('integrations');
        Route::patch('integrations', [IntegrationsController::class, 'update'])->name('integrations.update');
        Route::post('integrations/purge-orphans', [IntegrationsController::class, 'purgeOrphans'])
            ->name('integrations.purge-orphans');
    });
