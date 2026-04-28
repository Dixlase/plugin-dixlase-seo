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
    'heading' => '外部サービス連携',
    'description' => 'Google Analytics や Google Search Console などの外部サービスと連携します。',

    // セクション見出し
    'section_google' => 'Google サービス',

    // Google Analytics
    'google_analytics_id' => 'Google Analytics 測定ID',
    'google_analytics_id_help' => 'GA4の測定IDを入力すると、トラッキングコードが自動的にサイトに埋め込まれます。',
    'google_analytics_id_placeholder' => 'G-XXXXXXXXXX',

    // Search Console
    'google_site_verification' => 'Google Search Console 確認コード',
    'google_site_verification_help' => 'Search Consoleの「HTMLタグ」確認方法で表示されるcontent属性の値を入力してください。',
    'google_site_verification_placeholder' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',

    // フラッシュメッセージ
    'updated' => '外部サービス連携設定を更新しました。',
];
