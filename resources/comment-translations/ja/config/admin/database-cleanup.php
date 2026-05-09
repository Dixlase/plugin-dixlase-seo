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
    '/*
|--------------------------------------------------------------------------
| Database Cleanup Settings
|--------------------------------------------------------------------------
|
| Define the database table cleanup settings for the plugin.
| You can manage cleanup target tables and retention periods from the admin panel.
| Delete this file if it is not needed.
|
*/' => '/*
|--------------------------------------------------------------------------
| データベースクリーンアップ設定
|--------------------------------------------------------------------------
|
| プラグインのデータベーステーブルのクリーンアップ設定を定義します。
| 管理画面からクリーンアップ対象テーブルと保持期間を管理できます。
| このファイルが不要な場合は削除してください。
|
*/',
    '//         \'ja\' => \'Plugin cache data\',' => '//         \'ja\' => \'プラグインのキャッシュデータ\',',
    '//         \'ja\' => \'Plugin log records\',' => '//         \'ja\' => \'プラグインのログ記録\',',
    '//     // Optional: Date column type (\'datetime\' or \'timestamp\')' => '//     // オプション: 日付カラムの型 (\'datetime\' or \'timestamp\')',
    '//     // Optional: Enable/disable this cleanup type' => '//     // オプション: このクリーンアップタイプの有効/無効',
    '//     // Required: Column to use for date comparison' => '//     // 必須: 日付比較に使用するカラム',
    '//     // Required: Database table name' => '//     // 必須: データベーステーブル名',
    '//     // Required: Default retention period (days, null for expired only)' => '//     // 必須: デフォルトの保持期間（日数、nullの場合は期限切れのみ）',
    '//     // Required: Description (translation key or locale key array)' => '//     // 必須: 説明（翻訳キーまたはロケールキー配列）',

    // ----- metadata (underscore-prefixed; ignored as translation entries) -----
    '_review_status' => [
        '/*
|--------------------------------------------------------------------------
| Database Cleanup Settings
|--------------------------------------------------------------------------
|
| Define the database table cleanup settings for the plugin.
| You can manage cleanup target tables and retention periods from the admin panel.
| Delete this file if it is not needed.
|
*/' => 'machine',
        '//         \'ja\' => \'Plugin cache data\',' => 'machine',
        '//         \'ja\' => \'Plugin log records\',' => 'machine',
        '//     // Optional: Date column type (\'datetime\' or \'timestamp\')' => 'machine',
        '//     // Optional: Enable/disable this cleanup type' => 'machine',
        '//     // Required: Column to use for date comparison' => 'machine',
        '//     // Required: Database table name' => 'machine',
        '//     // Required: Default retention period (days, null for expired only)' => 'machine',
        '//     // Required: Description (translation key or locale key array)' => 'machine',
    ],
];
