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
     * Build robots.txt in auto-generation mode
     *
     * @param array<string, mixed> $settings
     */' => '/**
     * 自動生成モードのrobots.txtを構築する
     *
     * @param array<string, mixed> $settings
     */',
    '/**
     * Output robots.txt
     */' => '/**
     * robots.txtを出力する
     */',
    '/**
 * Controller for robots.txt
 */' => '/**
 * robots.txtのコントローラー
 */',
    '// Add URL if sitemap is enabled' => '// サイトマップが有効な場合はURLを追加',

    // ----- metadata (underscore-prefixed; ignored as translation entries) -----
    '_review_status' => [
        '/**
     * Build robots.txt in auto-generation mode
     *
     * @param array<string, mixed> $settings
     */' => 'machine',
        '/**
     * Output robots.txt
     */' => 'machine',
        '/**
 * Controller for robots.txt
 */' => 'machine',
        '// Add URL if sitemap is enabled' => 'machine',
    ],
];
