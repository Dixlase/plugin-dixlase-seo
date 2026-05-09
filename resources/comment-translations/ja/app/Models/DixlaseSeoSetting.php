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
     * Bulk retrieve multiple settings values
     *
     * @param array<string, mixed> $defaults Key-value pairs of defaults
     * @return array<string, mixed>
     */' => '/**
     * 複数の設定値を一括取得する
     *
     * @param array<string, mixed> $defaults キーとデフォルト値のペア
     * @return array<string, mixed>
     */',
    '/**
     * Bulk save multiple settings values
     *
     * @param array<string, mixed> $settings
     */' => '/**
     * 複数の設定値を一括保存する
     *
     * @param array<string, mixed> $settings
     */',
    '/**
     * Mass assignable attributes
     *
     * @var array<int, string>
     */' => '/**
     * 一括代入可能な属性
     *
     * @var array<int, string>
     */',
    '/**
     * Retrieve a settings value
     */' => '/**
     * 設定値を取得する
     */',
    '/**
     * Save a settings value
     */' => '/**
     * 設定値を保存する
     */',
    '/**
     * Table name
     *
     * @var string
     */' => '/**
     * テーブル名
     *
     * @var string
     */',
    '/**
 * Key-value model for SEO settings
 */' => '/**
 * SEO設定のキーバリュー型モデル
 */',

    // ----- metadata (underscore-prefixed; ignored as translation entries) -----
    '_review_status' => [
        '/**
     * Bulk retrieve multiple settings values
     *
     * @param array<string, mixed> $defaults Key-value pairs of defaults
     * @return array<string, mixed>
     */' => 'machine',
        '/**
     * Bulk save multiple settings values
     *
     * @param array<string, mixed> $settings
     */' => 'machine',
        '/**
     * Mass assignable attributes
     *
     * @var array<int, string>
     */' => 'machine',
        '/**
     * Retrieve a settings value
     */' => 'machine',
        '/**
     * Save a settings value
     */' => 'machine',
        '/**
     * Table name
     *
     * @var string
     */' => 'machine',
        '/**
 * Key-value model for SEO settings
 */' => 'machine',
    ],
];
