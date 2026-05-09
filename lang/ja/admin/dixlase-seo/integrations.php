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

return [
    'heading' => 'プラグイン連携',
    'description' => 'SEOメタ機能（メタディスクリプション・OGP画像）に対応したプラグインの有効/無効を切り替えます。',

    // Plugin list
    'section_plugins' => '連携プラグイン',
    'enable_for_plugin' => ':name で SEOメタ機能を有効にする',

    // Notice when unsupported
    'no_plugins_title' => 'SEOメタ対応プラグインが見つかりません',
    'no_plugins_description' => 'plugin.json で "capabilities": ["seo-meta"] を宣言しているプラグインがありません。対応プラグインがインストール・有効化されると、ここに自動的に表示されます。',

    // Cleanup of orphaned meta information
    'section_cleanup' => 'メンテナンス',
    'orphans_title' => '孤立メタ情報のクリーンアップ',
    'orphans_none' => '孤立メタ情報はありません。',
    'orphans_description' => '現在 SEOメタ機能に対応していない（アンインストール済み・無効化された・capability 未宣言の）プラグインのメタ情報が :count 件残っています。削除してもコンテンツ本体には影響しません。',
    'orphans_by_plugin' => ':slug: :count 件',
    'purge_orphans_button' => '孤立メタ情報を削除',
    'purge_orphans_confirm' => ':count 件の孤立メタ情報を削除します。この操作は取り消せません。本当に実行しますか？',

    // Flash message
    'updated' => 'プラグイン連携設定を更新しました。',
    'orphans_purged' => ':count 件の孤立メタ情報を削除しました。',
];
