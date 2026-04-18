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
    'heading' => 'メタタグ・OGP',
    'description' => 'サイト全体のメタタグ、OGP（SNSシェア時の表示）、組織情報（JSON-LD）を設定します。',

    // セクション見出し
    'section_general' => '基本設定',
    'section_ogp' => 'OGP / SNS設定',
    'section_organization' => '組織情報（JSON-LD）',

    // 基本設定
    'title_separator' => 'タイトル区切り文字',
    'title_separator_help' => 'ページタイトルとサイト名の間に表示される区切り文字です。例: ページ名 | サイト名',
    'default_description' => 'デフォルトのメタディスクリプション',
    'default_description_help' => '個別にディスクリプションが設定されていないページで使用されるデフォルトの説明文です。',

    // OGP設定
    'default_ogp_image' => 'デフォルトOGP画像',
    'default_ogp_image_help' => 'SNSでシェアされたときに表示されるデフォルト画像です。推奨サイズ: 1200x630px',
    'twitter_card_type' => 'X Cardタイプ',
    'twitter_card_type_help' => 'X（旧Twitter）でシェアされたときのカード表示形式です。旧Twitter Cards仕様に基づきます。',
    'twitter_card_summary' => 'Summary（小さいカード）',
    'twitter_card_summary_large' => 'Summary Large Image（大きいカード）',
    'twitter_site' => 'X アカウント',
    'twitter_site_help' => 'サイトに関連付けるX（旧Twitter）のアカウント名です（@なし）。',

    // 組織情報
    'organization_name' => '組織名',
    'organization_name_help' => '検索結果に表示される組織名です。',
    'organization_logo' => '組織ロゴ',
    'organization_logo_help' => '検索結果に表示される組織ロゴです。推奨: 112x112px以上の正方形。',
    'organization_url' => '組織URL',
    'organization_url_help' => '組織の公式WebサイトURLです。',

    // フラッシュメッセージ
    'updated' => 'メタタグ・OGP設定を更新しました。',
];
