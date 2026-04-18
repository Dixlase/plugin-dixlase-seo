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
            'base' => [
                'text' => 'dixlase-seo::admin/navigation.dixlase-seo.base',
                'route' => 'dixlase-seo::admin.seo.base',
                'icon' => 'fas fa-fw fa-cog',
                'can' => 'admin',
            ],
            'crawler' => [
                'text' => 'dixlase-seo::admin/navigation.dixlase-seo.crawler',
                'route' => 'dixlase-seo::admin.seo.crawler',
                'icon' => 'fas fa-fw fa-sitemap',
                'can' => 'admin',
            ],
            'external' => [
                'text' => 'dixlase-seo::admin/navigation.dixlase-seo.external',
                'route' => 'dixlase-seo::admin.seo.external',
                'icon' => 'fas fa-fw fa-plug',
                'can' => 'admin',
            ],
            'integrations' => [
                'text' => 'dixlase-seo::admin/navigation.dixlase-seo.integrations',
                'route' => 'dixlase-seo::admin.seo.integrations',
                'icon' => 'fas fa-fw fa-puzzle-piece',
                'can' => 'admin',
            ],
        ],
    ],
];
