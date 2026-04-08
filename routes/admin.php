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
use Plugins\DixlaseSEO\App\Http\Controllers\Admin\DixlaseSeoAdminSettingsController;

/*
|--------------------------------------------------------------------------
| DixlaseSEO Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('dixlase-seo')
    ->name('dixlase-seo::admin.dixlase-seo.')
    ->group(function () {
        Route::get('settings', [DixlaseSeoAdminSettingsController::class, 'settings'])
            ->name('settings');
        Route::patch('settings', [DixlaseSeoAdminSettingsController::class, 'updateSettings'])
            ->name('settings.update');
    });
