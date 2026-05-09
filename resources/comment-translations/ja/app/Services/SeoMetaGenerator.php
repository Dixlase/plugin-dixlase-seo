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
     * Build the site title
     */' => '/**
     * サイトタイトルを組み立てる
     */',
    '/**
     * Cache of settings values
     *
     * @var array<string, mixed>|null
     */' => '/**
     * 設定値のキャッシュ
     *
     * @var array<string, mixed>|null
     */',
    '/**
     * Convert image path to absolute URL
     */' => '/**
     * 画像パスを絶対URLに変換する
     */',
    '/**
     * Generate meta tags HTML for the front page
     */' => '/**
     * フロントページ用のメタタグHTMLを生成する
     */',
    '/**
     * Resolve media ID, URL, or path to absolute URL
     */' => '/**
     * メディアID・URL・パスのいずれかを絶対URLに解決する
     */',
    '/**
     * Retrieve settings values (lazy loading)
     *
     * @return array<string, mixed>
     */' => '/**
     * 設定値を取得する（遅延読み込み）
     *
     * @return array<string, mixed>
     */',
    '/**
 * Service for generating meta tags and OGP tags HTML
 */' => '/**
 * メタタグ・OGPタグのHTML生成サービス
 */',
    '// Basic meta tags (fall back to Core\'s site_description if plugin settings are empty)' => '// 基本メタタグ（プラグイン設定が空ならコアの site_description にフォールバック）',
    '// If numeric ID, resolve from Media model' => '// 数値IDならMediaモデルから解決',
    '// If string, treat as URL or path' => '// 文字列ならURLまたはパスとして扱う',
    '// OGP tags' => '// OGPタグ',
    '// Twitter Card tags' => '// Twitter Cardタグ',

    // ----- metadata (underscore-prefixed; ignored as translation entries) -----
    '_review_status' => [
        '/**
     * Build the site title
     */' => 'machine',
        '/**
     * Cache of settings values
     *
     * @var array<string, mixed>|null
     */' => 'machine',
        '/**
     * Convert image path to absolute URL
     */' => 'machine',
        '/**
     * Generate meta tags HTML for the front page
     */' => 'machine',
        '/**
     * Resolve media ID, URL, or path to absolute URL
     */' => 'machine',
        '/**
     * Retrieve settings values (lazy loading)
     *
     * @return array<string, mixed>
     */' => 'machine',
        '/**
 * Service for generating meta tags and OGP tags HTML
 */' => 'machine',
        '// Basic meta tags (fall back to Core\'s site_description if plugin settings are empty)' => 'machine',
        '// If numeric ID, resolve from Media model' => 'machine',
        '// If string, treat as URL or path' => 'machine',
        '// OGP tags' => 'machine',
        '// Twitter Card tags' => 'machine',
    ],
];
