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

return [
    'heading' => 'プラグイン連携',
    'description' => 'SEOメタ機能（メタディスクリプション・OGP画像）に対応したプラグインの有効/無効を切り替えます。',

    // 未対応時の案内
    'no_plugins_title' => 'SEOメタ対応プラグインが見つかりません',
    'no_plugins_description' => 'plugin.json で "capabilities": ["seo-meta"] を宣言しているプラグインがありません。対応プラグインがインストール・有効化されると、ここに自動的に表示されます。',

    // フラッシュメッセージ
    'updated' => 'プラグイン連携設定を更新しました。',
];
