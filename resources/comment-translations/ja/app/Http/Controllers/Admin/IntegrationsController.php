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
    '/**
     * Bulk delete orphaned meta information
     *
     * Deletes all meta information from plugins that do not currently declare the seo-meta capability.
     */' => '/**
     * 孤立メタ情報を一括削除する
     *
     * 現在 seo-meta capability を宣言していないプラグインのメタ情報を全削除する。
     */',
    '/**
     * Display the plugin integration settings screen
     */' => '/**
     * プラグイン連携設定画面を表示する
     */',
    '/**
     * Get list of active plugins that declare the seo-meta capability
     *
     * @return array<int, array{slug:string, name:string, description:string, enabled:bool}>
     */' => '/**
     * seo-meta capability を宣言した有効化プラグインの一覧を取得
     *
     * @return array<int, array{slug:string, name:string, description:string, enabled:bool}>
     */',
    '/**
     * Get summary of orphaned meta information
     *
     * @param  array<int, string>  $currentSlugs  Current seo-meta compatible plugin slugs
     * @return array{count:int, byPlugin: array<int, array{plugin_slug:string, count:int}>}
     */' => '/**
     * 孤立メタ情報の概要を取得
     *
     * @param  array<int, string>  $currentSlugs  現在の seo-meta 対応プラグインスラッグ
     * @return array{count:int, byPlugin: array<int, array{plugin_slug:string, count:int}>}
     */',
    '/**
     * Update plugin integration settings
     */' => '/**
     * プラグイン連携設定を更新する
     */',
    '/**
 * Admin panel controller for plugin integration settings
 *
 * Auto-detects active plugins that declare the `seo-meta` capability,
 * and allows toggling SEO meta functionality ON/OFF per plugin.
 * Also provides bulk deletion of orphaned meta information (from deleted plugins).
 */' => '/**
 * プラグイン連携設定の管理画面コントローラー
 *
 * `seo-meta` capability を宣言した有効化プラグインを自動検出し、
 * プラグインごとに SEOメタ機能の ON/OFF を切り替えられる。
 * 孤立したメタ情報（削除済みプラグイン由来）の一括削除機能も提供。
 */',
    '// Enabled if toggle is submitted, disabled if not submitted' => '// トグルが送信されていれば有効、未送信なら無効',

    // ----- metadata (underscore-prefixed; ignored as translation entries) -----
    '_review_status' => [
        '/**
     * Bulk delete orphaned meta information
     *
     * Deletes all meta information from plugins that do not currently declare the seo-meta capability.
     */' => 'machine',
        '/**
     * Display the plugin integration settings screen
     */' => 'machine',
        '/**
     * Get list of active plugins that declare the seo-meta capability
     *
     * @return array<int, array{slug:string, name:string, description:string, enabled:bool}>
     */' => 'machine',
        '/**
     * Get summary of orphaned meta information
     *
     * @param  array<int, string>  $currentSlugs  Current seo-meta compatible plugin slugs
     * @return array{count:int, byPlugin: array<int, array{plugin_slug:string, count:int}>}
     */' => 'machine',
        '/**
     * Update plugin integration settings
     */' => 'machine',
        '/**
 * Admin panel controller for plugin integration settings
 *
 * Auto-detects active plugins that declare the `seo-meta` capability,
 * and allows toggling SEO meta functionality ON/OFF per plugin.
 * Also provides bulk deletion of orphaned meta information (from deleted plugins).
 */' => 'machine',
        '// Enabled if toggle is submitted, disabled if not submitted' => 'machine',
    ],
];
