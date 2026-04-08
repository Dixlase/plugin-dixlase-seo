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

/*
|--------------------------------------------------------------------------
| 管理画面ナビゲーション設定
|--------------------------------------------------------------------------
*/

return [
    'dixlase-seo' => [
        '_insert_after' => 'media',
        'text' => 'dixlase-seo::admin/navigation.dixlase-seo.text',
        'icon' => 'fas fa-fw fa-search',
        'can' => 'admin',
        'children' => [
            'settings' => [
                'text' => 'dixlase-seo::admin/navigation.dixlase-seo.settings',
                'route' => 'dixlase-seo::admin.dixlase-seo.settings',
                'icon' => 'fas fa-fw fa-cog',
                'can' => 'admin',
            ],
        ],
    ],
];
