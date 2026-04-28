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

/*
|--------------------------------------------------------------------------
| DixlaseSEO デフォルト設定
|--------------------------------------------------------------------------
|
| SEOプラグインのデフォルト設定値を定義します。
| これらの値はデータベースに保存された設定で上書きされます。
|
*/

return [
    // タイトル区切り文字
    'title_separator' => '|',

    // デフォルトのメタディスクリプション
    'default_description' => '',

    // デフォルトのOGP画像パス（メディアライブラリのパス）
    'default_ogp_image' => '',

    // OGPタイプ（サイト全体）
    'ogp_type' => 'website',

    // Twitter Cardタイプ
    'twitter_card_type' => 'summary_large_image',

    // Twitter ユーザー名（@なし）
    'twitter_site' => '',

    // 組織名
    'organization_name' => '',

    // 組織ロゴURL
    'organization_logo' => '',

    // 組織URL
    'organization_url' => '',

    // robots.txtカスタム内容
    'robots_txt' => '',

    // robots.txt管理モード（'auto' または 'custom'）
    'robots_txt_mode' => 'auto',

    // サイトマップ有効化
    'sitemap_enabled' => true,

    // サイトマップの変更頻度
    'sitemap_changefreq' => 'weekly',

    // サイトマップの優先度
    'sitemap_priority' => '0.5',

    // Google Analytics 測定ID（GA4）
    'google_analytics_id' => '',

    // Google Search Console サイト確認コード
    'google_site_verification' => '',
];
