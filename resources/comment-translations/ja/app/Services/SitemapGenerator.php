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
     * Build XML for URL entry
     */' => '/**
     * URLエントリのXMLを構築する
     */',
    '/**
     * Collect URLs from LinkableProvider
     *
     * - Convert relative paths to absolute URLs
     * - Remove duplicates when the same URL is provided by multiple providers
     *
     * @return array<int, array{url: string, lastmod: string|null}>
     */' => '/**
     * LinkableProviderからURLを収集する
     *
     * - 相対パスの場合は絶対URLに変換
     * - 同一URLが複数プロバイダーから提供された場合は重複を除去
     *
     * @return array<int, array{url: string, lastmod: string|null}>
     */',
    '/**
     * Convert relative path to absolute URL
     */' => '/**
     * 相対パスを絶対URLに変換する
     */',
    '/**
     * Generate XML sitemap
     */' => '/**
     * XMLサイトマップを生成する
     */',
    '/**
 * XML sitemap generation service
 */' => '/**
 * XMLサイトマップの生成サービス
 */',
    '// Top page' => '// トップページ',
    '// URLs collected from LinkableProvider' => '// LinkableProviderから収集したURL',

    // ----- metadata (underscore-prefixed; ignored as translation entries) -----
    '_review_status' => [
        '/**
     * Build XML for URL entry
     */' => 'machine',
        '/**
     * Collect URLs from LinkableProvider
     *
     * - Convert relative paths to absolute URLs
     * - Remove duplicates when the same URL is provided by multiple providers
     *
     * @return array<int, array{url: string, lastmod: string|null}>
     */' => 'machine',
        '/**
     * Convert relative path to absolute URL
     */' => 'machine',
        '/**
     * Generate XML sitemap
     */' => 'machine',
        '/**
 * XML sitemap generation service
 */' => 'machine',
        '// Top page' => 'machine',
        '// URLs collected from LinkableProvider' => 'machine',
    ],
];
