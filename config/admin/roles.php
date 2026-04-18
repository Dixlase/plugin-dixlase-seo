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
 */

/*
|--------------------------------------------------------------------------
| プラグインのデフォルト権限設定
|--------------------------------------------------------------------------
*/

use App\Enums\MemberRole;

return [
    'permissions' => [
        // 基本設定（管理者のみ）
        'settings.dixlase-seo.base' => [
            'access_roles' => MemberRole::ADMIN->value,
            'view_roles' => MemberRole::ADMIN->value,
        ],
        // サイトマップ・robots.txt設定（管理者のみ）
        'settings.dixlase-seo.sitemap' => [
            'access_roles' => MemberRole::ADMIN->value,
            'view_roles' => MemberRole::ADMIN->value,
        ],
        // 外部サービス連携設定（管理者のみ）
        'settings.dixlase-seo.external' => [
            'access_roles' => MemberRole::ADMIN->value,
            'view_roles' => MemberRole::ADMIN->value,
        ],
        // プラグイン連携設定（管理者のみ）
        'settings.dixlase-seo.integrations' => [
            'access_roles' => MemberRole::ADMIN->value,
            'view_roles' => MemberRole::ADMIN->value,
        ],
    ],
];
