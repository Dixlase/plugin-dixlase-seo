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
     * Display SEO basic settings screen
     */' => '/**
     * SEO基本設定画面を表示する
     */',
    '/**
     * Update SEO basic settings
     */' => '/**
     * SEO基本設定を更新する
     */',
    '/**
 * Admin panel controller for SEO basic settings
 *
 * - Title separator, default description
 * - OGP / SNS (OGP image, X Card, X account)
 * - Organization info (JSON-LD: organization name, logo, URL)
 */' => '/**
 * SEO基本設定の管理画面コントローラー
 *
 * - タイトル区切り・デフォルトディスクリプション
 * - OGP / SNS（OGP画像・X Card・X アカウント）
 * - 組織情報（JSON-LD: 組織名・ロゴ・URL）
 */',
    '// Default OGP image: resolve Media model from media ID' => '// デフォルトOGP画像: メディアIDからMediaモデルを解決',
    '// Organization logo: resolve Media model from media ID' => '// 組織ロゴ: メディアIDからMediaモデルを解決',
    '// Provide Core site description as fallback' => '// コアのサイト説明をフォールバックとして提供',
    '// X (formerly Twitter) Card type options' => '// X（旧Twitter）Cardタイプの選択肢',

    // ----- metadata (underscore-prefixed; ignored as translation entries) -----
    '_review_status' => [
        '/**
     * Display SEO basic settings screen
     */' => 'machine',
        '/**
     * Update SEO basic settings
     */' => 'machine',
        '/**
 * Admin panel controller for SEO basic settings
 *
 * - Title separator, default description
 * - OGP / SNS (OGP image, X Card, X account)
 * - Organization info (JSON-LD: organization name, logo, URL)
 */' => 'machine',
        '// Default OGP image: resolve Media model from media ID' => 'machine',
        '// Organization logo: resolve Media model from media ID' => 'machine',
        '// Provide Core site description as fallback' => 'machine',
        '// X (formerly Twitter) Card type options' => 'machine',
    ],
];
