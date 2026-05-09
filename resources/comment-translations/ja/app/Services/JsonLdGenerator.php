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
     * Build Organization schema
     *
     * @return array<string, mixed>|null
     */' => '/**
     * Organizationスキーマを構築する
     *
     * @return array<string, mixed>|null
     */',
    '/**
     * Build WebSite schema
     *
     * @return array<string, mixed>|null
     */' => '/**
     * WebSiteスキーマを構築する
     *
     * @return array<string, mixed>|null
     */',
    '/**
     * Generate JSON-LD script tag
     */' => '/**
     * JSON-LDスクリプトタグを生成する
     */',
    '/**
     * Resolve organization logo value (media ID, URL, or path) to absolute URL
     */' => '/**
     * 組織ロゴの値（メディアID・URL・パス）を絶対URLに解決する
     */',
    '/**
     * Retrieve settings values
     *
     * @return array<string, mixed>
     */' => '/**
     * 設定値を取得する
     *
     * @return array<string, mixed>
     */',
    '/**
     * Settings value cache
     *
     * @var array<string, mixed>|null
     */' => '/**
     * 設定値のキャッシュ
     *
     * @var array<string, mixed>|null
     */',
    '/**
 * JSON-LD structured data generation service (WebSite + Organization)
 */' => '/**
 * JSON-LD構造化データの生成サービス（WebSite + Organization）
 */',

    // ----- metadata (underscore-prefixed; ignored as translation entries) -----
    '_review_status' => [
        '/**
     * Build Organization schema
     *
     * @return array<string, mixed>|null
     */' => 'machine',
        '/**
     * Build WebSite schema
     *
     * @return array<string, mixed>|null
     */' => 'machine',
        '/**
     * Generate JSON-LD script tag
     */' => 'machine',
        '/**
     * Resolve organization logo value (media ID, URL, or path) to absolute URL
     */' => 'machine',
        '/**
     * Retrieve settings values
     *
     * @return array<string, mixed>
     */' => 'machine',
        '/**
     * Settings value cache
     *
     * @var array<string, mixed>|null
     */' => 'machine',
        '/**
 * JSON-LD structured data generation service (WebSite + Organization)
 */' => 'machine',
    ],
];
