<?php

/**
 * This file is part of Dixlase SEO.
 *
 * Copyright (C) 2026 exc-D inc.
 * https://exc-d.com
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
    'heading' => 'サイトマップ',
    'description' => '検索エンジンクローラー向けのXMLサイトマップとrobots.txtを設定します。',

    // セクション見出し
    'section_sitemap' => 'サイトマップ',
    'section_robots' => 'robots.txt',

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
    'updated' => 'サイトマップ・robots.txt設定を更新しました。',
];
