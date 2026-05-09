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
     * Bulk delete all meta information for a specified plugin
     */' => '/**
     * 指定プラグインの全メタ情報を一括削除
     */',
    '/**
     * Check if SEO meta functionality is enabled for a specified plugin
     *
     * Determined by a two-step check:
     * 1. Does the target plugin declare `"capabilities": ["seo-meta"]` in plugin.json?
     * 2. Is integration.{plugin_slug}.enabled not disabled in SEO plugin settings?
     *    (defaults to enabled if not configured)
     *
     * Plugins that do not declare the capability are disabled regardless of admin settings.
     * This prevents accidental use of SEO meta and allows new plugins to automatically
     * gain support simply by declaring seo-meta.
     */' => '/**
     * 指定プラグインのSEOメタ機能が有効化されているかを確認
     *
     * 2段階のチェックで判定：
     * 1. 対象プラグインが plugin.json に `"capabilities": ["seo-meta"]` を宣言しているか
     * 2. SEOプラグインの設定で integration.{plugin_slug}.enabled が無効化されていないか
     *    （未設定時はデフォルト有効）
     *
     * capability を宣言していないプラグインは admin 設定に関係なく無効。
     * これにより「意図せずSEOメタを使ってしまう」事故を防ぎ、
     * 新規プラグインが seo-meta を宣言するだけで自動的に対応可能になる。
     */',
    '/**
     * Delete a single meta information record
     */' => '/**
     * 単一のメタ情報を削除
     */',
    '/**
     * Get meta information for a specified entity of a specified plugin
     */' => '/**
     * 指定プラグインの指定エンティティのメタ情報を取得
     */',
    '/**
     * Key format for per-plugin SEO enablement settings
     *
     * Example: integration.dixlase-pages.enabled
     */' => '/**
     * プラグイン別SEO有効化設定のキー形式
     *
     * 例: integration.dixlase-pages.enabled
     */',
    '/**
     * Plugin capability required to declare SEO meta support
     */' => '/**
     * SEOメタ対応を宣言するために必要なプラグイン capability
     */',
    '/**
     * Save (upsert) meta information
     *
     * If a DTO with all fields null is passed, the record will be deleted.
     */' => '/**
     * メタ情報を保存（upsert）
     *
     * 全フィールドが null の DTO が渡された場合はレコードを削除します。
     */',
    '/**
 * DixlaseSEO implementation of SeoMetaProviderInterface
 *
 * Reads and writes the dls_plg_dixlase_seo_meta table to manage
 * SEO meta information for plugin-generated content entities.
 */' => '/**
 * SeoMetaProviderInterface の DixlaseSEO による実装
 *
 * dls_plg_dixlase_seo_meta テーブルを読み書きして
 * プラグイン生成コンテンツ単位のSEOメタ情報を管理します。
 */',

    // ----- metadata (underscore-prefixed; ignored as translation entries) -----
    '_review_status' => [
        '/**
     * Bulk delete all meta information for a specified plugin
     */' => 'machine',
        '/**
     * Check if SEO meta functionality is enabled for a specified plugin
     *
     * Determined by a two-step check:
     * 1. Does the target plugin declare `"capabilities": ["seo-meta"]` in plugin.json?
     * 2. Is integration.{plugin_slug}.enabled not disabled in SEO plugin settings?
     *    (defaults to enabled if not configured)
     *
     * Plugins that do not declare the capability are disabled regardless of admin settings.
     * This prevents accidental use of SEO meta and allows new plugins to automatically
     * gain support simply by declaring seo-meta.
     */' => 'machine',
        '/**
     * Delete a single meta information record
     */' => 'machine',
        '/**
     * Get meta information for a specified entity of a specified plugin
     */' => 'machine',
        '/**
     * Key format for per-plugin SEO enablement settings
     *
     * Example: integration.dixlase-pages.enabled
     */' => 'machine',
        '/**
     * Plugin capability required to declare SEO meta support
     */' => 'machine',
        '/**
     * Save (upsert) meta information
     *
     * If a DTO with all fields null is passed, the record will be deleted.
     */' => 'machine',
        '/**
 * DixlaseSEO implementation of SeoMetaProviderInterface
 *
 * Reads and writes the dls_plg_dixlase_seo_meta table to manage
 * SEO meta information for plugin-generated content entities.
 */' => 'machine',
    ],
];
