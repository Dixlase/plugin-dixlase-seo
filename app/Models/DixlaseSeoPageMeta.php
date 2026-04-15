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

namespace Plugins\DixlaseSEO\App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * プラグイン生成ページ単位のSEOメタ情報モデル
 *
 * (plugin_slug, entity_id) のペアで一意に識別される。
 */
class DixlaseSeoPageMeta extends Model
{
    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'dls_plg_dixlase_seo_page_meta';

    /**
     * 一括代入可能な属性
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'plugin_slug',
        'entity_id',
        'description',
        'ogp_media_id',
    ];

    /**
     * キャストする属性
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ogp_media_id' => 'integer',
        ];
    }
}
