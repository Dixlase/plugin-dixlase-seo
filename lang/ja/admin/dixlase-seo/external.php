<?php

/**
 * This file is part of Dixlase SEO.
 *
 * Copyright (C) 2026 exc-D inc. and Dixlase contributors
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

    // Section heading
    'section_google' => 'Google サービス',

    // Google Analytics
    'google_analytics_id' => 'Google Analytics 測定ID',
    'google_analytics_id_help' => 'GA4の測定IDを入力すると、トラッキングコードが自動的にサイトに埋め込まれます。',
    'google_analytics_id_placeholder' => 'G-XXXXXXXXXX',

    // Cookie 同意バナーの状態を GA 欄付近に表示。検出は汎用（core の同意契約を
    // bind する任意のプラグインを認識）。
    'consent_active_title' => 'Cookie 同意バナーを検出しました',
    'consent_active_body' => 'Cookie 同意バナーが有効です。Google Analytics は、訪問者が「分析」同意カテゴリを許可した場合にのみ動作します。',
    'consent_active_off_title' => '同意バナーを検出しましたが、同意は適用されていません',
    'consent_active_off_body' => '同意バナーは有効ですが、上のトグルがオフのため、Google Analytics は訪問者の選択に関わらず全員に対して動作します。「分析」に同意した訪問者にのみ動作させるには、トグルをオンにしてください。',
    'consent_warning_title' => 'Cookie 同意バナーが有効になっていません',
    'consent_warning_legal' => 'Google Analytics は解析用 Cookie を使用します。EU・英国・ブラジルなど多くの国・地域では、解析 Cookie の利用に訪問者の事前同意が必須で、同意バナー無しでの解析実行は法令違反となります。',
    'consent_warning_not_installed' => 'Cookie 同意プラグイン（例: Dixlase Cookie）をインストールしてバナーを表示することを推奨します。バナーを有効にし、上の「Analytics で訪問者の Cookie 同意を尊重する」をオンにすると、訪問者が「分析」カテゴリを許可した場合のみ解析が動作します。それまでは全訪問者に対してタグが発火し続けます。',
    'consent_warning_banner_off' => 'Cookie 同意プラグインはインストール済みですが、バナーが無効になっています。バナーを有効にし、上の「Analytics で訪問者の Cookie 同意を尊重する」をオンにすると、訪問者が「分析」カテゴリを許可した場合のみ解析が動作します。バナーが無効な間は、全訪問者に対してタグが発火し続けます。',

    // 同意尊重トグル
    'respect_cookie_consent' => 'Analytics で訪問者の Cookie 同意を尊重する',
    'respect_cookie_consent_help' => 'オンの場合、訪問者が「分析」Cookie に同意するまで Analytics は動作しません（「必須」だけの同意では動作しません）。オフにすると、同意の有無に関わらず動作します。',

    // Search Console
    'google_site_verification' => 'Google Search Console 確認コード',
    'google_site_verification_help' => 'Search Consoleの「HTMLタグ」確認方法で表示されるcontent属性の値を入力してください。',
    'google_site_verification_placeholder' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',

    // Flash message
    'updated' => '外部サービス連携設定を更新しました。',
];
