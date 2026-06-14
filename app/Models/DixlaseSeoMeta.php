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

namespace Plugins\DixlaseSEO\App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * SEO meta information model for plugin-generated content units
 *
 * Uniquely identified by the pair (plugin_slug, entity_id). Stores the
 * primary-locale meta description; per-locale translations are owned by the
 * content's own plugin (e.g. DixlasePages) as a `seo_description`
 * translatable field, so they appear in that page's translation editor.
 */
class DixlaseSeoMeta extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'plg_dixlase_seo_meta';

    /**
     * Mass assignable attributes
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
     * Attributes to cast
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
