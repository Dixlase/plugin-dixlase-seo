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
     * Bootstrap services
     */' => '/**
     * サービスを起動する
     */',
    '/**
     * Register CSP policy
     */' => '/**
     * CSPポリシーを登録する
     */',
    '/**
     * Register middleware
     */' => '/**
     * ミドルウェアを登録する
     */',
    '/**
     * Register services
     */' => '/**
     * サービスを登録する
     */',
    '/**
     * Retrieve CSP directives
     *
     * @return array<string, array<string>>
     */' => '/**
     * CSPディレクティブを取得する
     *
     * @return array<string, array<string>>
     */',
    '/**
 * ServiceProvider for DixlaseSEO plugin
 */' => '/**
 * DixlaseSEOプラグインのServiceProvider
 */',
    '// Add SEO meta tag injection middleware to web group (runs on all front pages)' => '// SEOメタタグ注入ミドルウェアをwebグループに追加（全フロントページで動作）',
    '// Inject LinkableProvider into SitemapGenerator' => '// SitemapGeneratorにLinkableProviderを注入',
    '// Merge settings file' => '// 設定ファイルをマージ',
    '// Note: $app->tagged() returns an iterator regardless of whether tags are registered,' => '// Note: $app->tagged() はタグ登録の有無にかかわらず iterator を返すため、',
    '// Other plugins can resolve via app(SeoMetaProviderInterface::class)' => '// 他プラグインは app(SeoMetaProviderInterface::class) で解決可能',
    '// Public assets' => '// 公開可能なアセット',
    '// Register CSP policy' => '// CSPポリシーの登録',
    '// Register Contract implementation for per-content SEO meta information' => '// コンテンツ単位SEOメタ情報のContract実装を登録',
    '// Register anonymous Blade components (available from other plugins as <x-dixlase-seo::name />)' => '// 匿名Bladeコンポーネントの登録（他プラグインから <x-dixlase-seo::name /> で利用可能）',
    '// Register middleware' => '// ミドルウェアの登録',
    '// Register migrations' => '// マイグレーションの登録',
    '// Register service as singleton' => '// サービスをシングルトンで登録',
    '// Register translation files' => '// 翻訳ファイルの登録',
    '// Register views' => '// ビューの登録',
    '// bound() check is unnecessary (bound() always returns false as it checks bindings, not tags)' => '// bound() チェックは不要（bound() はタグではなくバインディングを見るため常にfalse）',

    // ----- metadata (underscore-prefixed; ignored as translation entries) -----
    '_review_status' => [
        '/**
     * Bootstrap services
     */' => 'machine',
        '/**
     * Register CSP policy
     */' => 'machine',
        '/**
     * Register middleware
     */' => 'machine',
        '/**
     * Register services
     */' => 'machine',
        '/**
     * Retrieve CSP directives
     *
     * @return array<string, array<string>>
     */' => 'machine',
        '/**
 * ServiceProvider for DixlaseSEO plugin
 */' => 'machine',
        '// Add SEO meta tag injection middleware to web group (runs on all front pages)' => 'machine',
        '// Inject LinkableProvider into SitemapGenerator' => 'machine',
        '// Merge settings file' => 'machine',
        '// Note: $app->tagged() returns an iterator regardless of whether tags are registered,' => 'machine',
        '// Other plugins can resolve via app(SeoMetaProviderInterface::class)' => 'machine',
        '// Public assets' => 'machine',
        '// Register CSP policy' => 'machine',
        '// Register Contract implementation for per-content SEO meta information' => 'machine',
        '// Register anonymous Blade components (available from other plugins as <x-dixlase-seo::name />)' => 'machine',
        '// Register middleware' => 'machine',
        '// Register migrations' => 'machine',
        '// Register service as singleton' => 'machine',
        '// Register translation files' => 'machine',
        '// Register views' => 'machine',
        '// bound() check is unnecessary (bound() always returns false as it checks bindings, not tags)' => 'machine',
    ],
];
