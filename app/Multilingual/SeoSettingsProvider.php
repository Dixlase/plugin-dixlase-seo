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

namespace Plugins\DixlaseSEO\App\Multilingual;

use App\Contracts\Multilingual\TranslatableContentProvider;
use App\Helpers\LocaleHelper;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;

/**
 * Primary-locale value source for the DixlaseSEO plugin's translatable
 * site-wide settings (currently `default_description`).
 *
 * Registered in plugin.json as the `provider` for the
 * `dixlase-seo:settings` singleton type. DixlaseMultilingual calls this
 * when no per-locale translation exists for the requested locale, so the
 * front end falls back to the primary value stored in the plugin's
 * existing key-value `plg_dixlase_seo_settings` table.
 *
 * Mirrors `Plugins\DixlaseInquiry\App\Multilingual\InquirySettingsProvider`
 * and `Themes\DixlaseOnePage\App\Multilingual\DixlaseOnePageSettingsProvider`
 * — both read-only bridges between the central translation resolver and
 * extension-owned storage.
 */
class SeoSettingsProvider implements TranslatableContentProvider
{
    public function getPrimaryValue(string $field): ?string
    {
        $value = DixlaseSeoSetting::getValue($field, config('dixlase_seo.'.$field));

        return $value === null ? null : (string) $value;
    }

    /**
     * The locale the primary-stored SEO settings are written in.
     *
     * DixlaseSEO has no authoring-locale setting of its own, so the
     * primary value is assumed to be written in the site's default
     * locale. Returns null only when LocaleHelper is unavailable; the
     * helper / central editor treat null as "no primary locale
     * configured" (every enabled locale editable).
     */
    public function getPrimaryLocale(): ?string
    {
        try {
            return LocaleHelper::getSiteDefaultLocale();
        } catch (\Throwable) {
            return null;
        }
    }
}
