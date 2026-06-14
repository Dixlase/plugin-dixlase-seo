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

namespace Plugins\DixlaseSEO\App\Services;

/**
 * Request-scoped holder for the content entity currently being rendered.
 *
 * A front-end view (e.g. DixlasePages / DixlaseLegal `front/page.blade.php`)
 * declares which `(plugin_slug, entity_id)` it represents via the
 * `dls_seo_set_entity()` helper. SeoMetaGenerator then reads that entity
 * when building the `<head>` so it can emit the entity's own (locale-aware)
 * meta description instead of only the site-wide default.
 *
 * Registered as a container singleton, so it lives for the duration of one
 * request and is empty by default (site-wide default description applies).
 */
class SeoContext
{
    /**
     * @var array{plugin_slug: string, entity_id: string}|null
     */
    private ?array $entity = null;

    public function setEntity(string $pluginSlug, string $entityId): void
    {
        $this->entity = [
            'plugin_slug' => $pluginSlug,
            'entity_id' => $entityId,
        ];
    }

    /**
     * @return array{plugin_slug: string, entity_id: string}|null
     */
    public function getEntity(): ?array
    {
        return $this->entity;
    }
}
