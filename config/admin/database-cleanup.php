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

/*
|--------------------------------------------------------------------------
| Database Cleanup Settings
|--------------------------------------------------------------------------
|
| Define the database table cleanup settings for the plugin.
| You can manage cleanup target tables and retention periods from the admin panel.
| Delete this file if it is not needed.
|
*/

return [
    // 'example_logs' => [
    //     // Required: Database table name
    //     'table' => 'dixlase-seo_logs',
    //
    //     // Required: Column to use for date comparison
    //     'date_column' => 'created_at',
    //
    //     // Required: Default retention period (days, null for expired only)
    //     'default_days' => 30,
    //
    //     // Required: Description (translation key or locale key array)
    //     'description' => [
    //         'ja' => 'Plugin log records',
    //         'en' => 'Plugin log records',
    //     ],
    //
    //     // Optional: Enable/disable this cleanup type
    //     'enabled' => true,
    //
    //     // Optional: Date column type ('datetime' or 'timestamp')
    //     'date_column_type' => 'datetime',
    // ],

    // 'example_cache' => [
    //     'table' => 'dixlase-seo_cache',
    //     'date_column' => 'expires_at',
    //     'default_days' => null,
    //     'description' => [
    //         'ja' => 'Plugin cache data',
    //         'en' => 'Plugin cache data',
    //     ],
    //     'enabled' => true,
    //     'date_column_type' => 'timestamp',
    // ],
];