<?php

/**
 * This file is part of DixlaseSEO.
 *
 * Copyright (C) 2026
 * example.com
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
    'heading' => 'SEO設定',
    'description' => 'サイト全体のSEO設定を管理します。メタタグ、OGP、構造化データ、サイトマップ、robots.txtの設定ができます。',

    // セクション見出し
    'section_general' => '基本設定',
    'section_ogp' => 'OGP / SNS設定',
    'section_organization' => '組織情報（JSON-LD）',
    'section_sitemap' => 'サイトマップ',
    'section_robots' => 'robots.txt',

    // 基本設定
    'title_separator' => 'タイトル区切り文字',
    'title_separator_help' => 'ページタイトルとサイト名の間に表示される区切り文字です。例: ページ名 | サイト名',
    'default_description' => 'デフォルトのメタディスクリプション',
    'default_description_help' => '個別にディスクリプションが設定されていないページで使用されるデフォルトの説明文です。',

    // OGP設定
    'default_ogp_image' => 'デフォルトOGP画像',
    'default_ogp_image_help' => 'SNSでシェアされたときに表示されるデフォルト画像です。推奨サイズ: 1200x630px',
    'twitter_card_type' => 'Twitter Cardタイプ',
    'twitter_card_type_help' => 'Twitterでシェアされたときのカード表示形式です。',
    'twitter_card_summary' => 'Summary（小さいカード）',
    'twitter_card_summary_large' => 'Summary Large Image（大きいカード）',
    'twitter_site' => 'Twitter アカウント',
    'twitter_site_help' => 'サイトに関連付けるTwitterアカウント名です（@なし）。',

    // 組織情報
    'organization_name' => '組織名',
    'organization_name_help' => '検索結果に表示される組織名です。',
    'organization_logo' => '組織ロゴ',
    'organization_logo_help' => '検索結果に表示される組織ロゴのURLです。推奨: 112x112px以上の正方形。',
    'organization_url' => '組織URL',
    'organization_url_help' => '組織の公式WebサイトURLです。',

    // サイトマップ
    'sitemap_enabled' => 'XMLサイトマップ',
    'sitemap_enabled_help' => '有効にすると /sitemap.xml でXMLサイトマップが生成されます。',
    'sitemap_changefreq' => '更新頻度',
    'sitemap_changefreq_help' => 'サイトマップに記載される更新頻度のヒントです。',
    'sitemap_priority' => '優先度',
    'sitemap_priority_help' => 'サイトマップに記載されるページの優先度です（0.0〜1.0）。',

    // 更新頻度の選択肢
    'changefreq_always' => '常に',
    'changefreq_hourly' => '毎時',
    'changefreq_daily' => '毎日',
    'changefreq_weekly' => '毎週',
    'changefreq_monthly' => '毎月',
    'changefreq_yearly' => '毎年',
    'changefreq_never' => 'なし',

    // robots.txt
    'robots_txt_mode' => 'robots.txt管理モード',
    'robots_txt_mode_help' => '自動モードではサイトマップURLを含む標準的なrobots.txtが生成されます。',
    'robots_txt_mode_auto' => '自動生成',
    'robots_txt_mode_auto_desc' => '標準的なrobots.txtを自動生成します',
    'robots_txt_mode_custom' => 'カスタム',
    'robots_txt_mode_custom_desc' => '自由に内容を編集できます',
    'robots_txt_content' => 'robots.txt内容',
    'robots_txt_content_help' => 'robots.txtに出力する内容をカスタマイズできます。',

    // フラッシュメッセージ
    'updated' => 'SEO設定を更新しました。',
];
