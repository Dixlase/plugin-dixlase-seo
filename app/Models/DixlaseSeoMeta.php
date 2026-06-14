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

use App\Helpers\LocaleHelper;
use App\Traits\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * SEO meta information model for plugin-generated content units
 *
 * Uniquely identified by the pair (plugin_slug, entity_id).
 *
 * The `description` is translatable: the primary-locale value lives in the
 * `description` column, and per-locale overrides are stored in the central
 * DixlaseMultilingual translations table (declared as the
 * `dixlase-seo:meta` collection type in plugin.json). When the
 * multilingual plugin is absent, TranslatableTrait transparently returns
 * the column value.
 */
class DixlaseSeoMeta extends Model
{
    use TranslatableTrait;

    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'plg_dixlase_seo_meta';

    /**
     * Translatable fields (resolved per locale via TranslatableTrait).
     *
     * @var array<int, string>
     */
    protected array $translatable = [
        'description',
    ];

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

    /**
     * Human-readable row label for the DixlaseMultilingual central
     * translation manager (declared as `label_field` in plugin.json).
     *
     * SEO meta rows have no title of their own, so the label combines the
     * owning entity reference with an excerpt of the primary description.
     */
    public function getLabelAttribute(): string
    {
        $base = $this->plugin_slug.' #'.$this->entity_id;
        $description = (string) ($this->getRawOriginal('description') ?? '');

        return $description !== ''
            ? $base.' — '.Str::limit($description, 40)
            : $base;
    }

    /**
     * Authoring locale of the primary `description` value.
     *
     * SEO meta has no per-content language of its own — its baseline is the
     * site's primary locale (the language set in the site base settings).
     * Exposing it as the `lang` attribute makes the DixlaseMultilingual
     * central translation manager exclude that locale from the locale
     * selector, so operators only translate INTO other languages — matching
     * the per-content `lang` column that collection types like Pages/Menus
     * carry. This is read by the manager only; it never persists.
     */
    public function getLangAttribute(): string
    {
        try {
            return LocaleHelper::getSiteDefaultLocale();
        } catch (\Throwable) {
            return (string) config('app.fallback_locale', 'en');
        }
    }
}
