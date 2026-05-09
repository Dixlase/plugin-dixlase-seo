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

use App\Contracts\PluginIntegration\SeoMetaProviderInterface;
use App\DTO\PluginIntegration\SeoMetaDTO;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoMeta;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;

/**
 * DixlaseSEO implementation of SeoMetaProviderInterface
 *
 * Reads and writes the dls_plg_dixlase_seo_meta table to manage
 * SEO meta information for plugin-generated content entities.
 */
class DixlaseSeoMetaProvider implements SeoMetaProviderInterface
{
    /**
     * Key format for per-plugin SEO enablement settings
     *
     * Example: integration.dixlase-pages.enabled
     */
    private const ENABLED_SETTING_KEY_FORMAT = 'integration.%s.enabled';

    /**
     * Get meta information for a specified entity of a specified plugin
     */
    public function getMeta(string $pluginSlug, string $entityId): ?SeoMetaDTO
    {
        $record = DixlaseSeoMeta::query()
            ->where('plugin_slug', $pluginSlug)
            ->where('entity_id', $entityId)
            ->first();

        if (! $record) {
            return null;
        }

        return new SeoMetaDTO(
            description: $record->description,
            ogpMediaId: $record->ogp_media_id,
        );
    }

    /**
     * Save (upsert) meta information
     *
     * If a DTO with all fields null is passed, the record will be deleted.
     */
    public function saveMeta(string $pluginSlug, string $entityId, SeoMetaDTO $meta): void
    {
        if ($meta->isEmpty()) {
            $this->deleteMeta($pluginSlug, $entityId);

            return;
        }

        DixlaseSeoMeta::updateOrCreate(
            [
                'plugin_slug' => $pluginSlug,
                'entity_id' => $entityId,
            ],
            [
                'description' => $meta->description,
                'ogp_media_id' => $meta->ogpMediaId,
            ],
        );
    }

    /**
     * Delete a single meta information record
     */
    public function deleteMeta(string $pluginSlug, string $entityId): void
    {
        DixlaseSeoMeta::query()
            ->where('plugin_slug', $pluginSlug)
            ->where('entity_id', $entityId)
            ->delete();
    }

    /**
     * Bulk delete all meta information for a specified plugin
     */
    public function purgeByPlugin(string $pluginSlug): int
    {
        return DixlaseSeoMeta::query()
            ->where('plugin_slug', $pluginSlug)
            ->delete();
    }

    /**
     * Plugin capability required to declare SEO meta support
     */
    private const SEO_META_CAPABILITY = 'seo-meta';

    /**
     * Check if SEO meta functionality is enabled for a specified plugin
     *
     * Determined by a two-step check:
     * 1. Does the target plugin declare `"capabilities": ["seo-meta"]` in plugin.json?
     * 2. Is integration.{plugin_slug}.enabled not disabled in SEO plugin settings?
     *    (defaults to enabled if not configured)
     *
     * Plugins that do not declare the capability are disabled regardless of admin settings.
     * This prevents accidental use of SEO meta and allows new plugins to automatically
     * gain support simply by declaring seo-meta.
     */
    public function isEnabledForPlugin(string $pluginSlug): bool
    {
        if (! \App\Helpers\PluginHelper::pluginHasCapability($pluginSlug, self::SEO_META_CAPABILITY)) {
            return false;
        }

        $key = sprintf(self::ENABLED_SETTING_KEY_FORMAT, $pluginSlug);
        $value = DixlaseSeoSetting::getValue($key, '1');

        return $value !== '0' && $value !== false;
    }
}
