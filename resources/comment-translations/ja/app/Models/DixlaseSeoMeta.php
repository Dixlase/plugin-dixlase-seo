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
     * Attributes to cast
     *
     * @return array<string, string>
     */' => '/**
     * キャストする属性
     *
     * @return array<string, string>
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
     * Table name
     *
     * @var string
     */' => '/**
     * テーブル名
     *
     * @var string
     */',
    '/**
 * SEO meta information model for plugin-generated content units
 *
 * Uniquely identified by the pair (plugin_slug, entity_id).
 */' => '/**
 * プラグイン生成コンテンツ単位のSEOメタ情報モデル
 *
 * (plugin_slug, entity_id) のペアで一意に識別される。
 */',

    // ----- metadata (underscore-prefixed; ignored as translation entries) -----
    '_review_status' => [
        '/**
     * Attributes to cast
     *
     * @return array<string, string>
     */' => 'machine',
        '/**
     * Mass assignable attributes
     *
     * @var array<int, string>
     */' => 'machine',
        '/**
     * Table name
     *
     * @var string
     */' => 'machine',
        '/**
 * SEO meta information model for plugin-generated content units
 *
 * Uniquely identified by the pair (plugin_slug, entity_id).
 */' => 'machine',
    ],
];
