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

namespace Plugins\DixlaseSEO\App\Http\Controllers\Admin;

use App\Helpers\PluginHelper;
use App\Http\Controllers\Admin\AdminLoggedInController;
use App\Models\Plugin;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Plugins\DixlaseSEO\App\Http\Requests\Admin\UpdateIntegrationsRequest;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoMeta;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;

/**
 * Admin panel controller for plugin integration settings
 *
 * Auto-detects active plugins that declare the `seo-meta` capability,
 * and allows toggling SEO meta functionality ON/OFF per plugin.
 * Also provides bulk deletion of orphaned meta information (from deleted plugins).
 */
class IntegrationsController extends AdminLoggedInController
{
    private const ENABLED_SETTING_KEY_FORMAT = 'integration.%s.enabled';

    private const REQUIRED_CAPABILITY = 'seo-meta';

    /**
     * Display the plugin integration settings screen
     */
    public function show(): View
    {
        $plugins = $this->getSeoMetaPlugins();
        $orphans = $this->getOrphanSummary(array_column($plugins, 'slug'));

        return view('dixlase-seo::admin.integrations', array_merge($this->viewParams, [
            'plugins' => $plugins,
            'orphans' => $orphans,
        ]));
    }

    /**
     * Update plugin integration settings
     */
    public function update(UpdateIntegrationsRequest $request): RedirectResponse
    {
        $toggles = $request->validated()['integration'] ?? [];
        $currentSlugs = PluginHelper::getEnabledPluginSlugsByCapability(self::REQUIRED_CAPABILITY);

        foreach ($currentSlugs as $slug) {
            $key = sprintf(self::ENABLED_SETTING_KEY_FORMAT, $slug);
            // Enabled if toggle is submitted, disabled if not submitted
            $value = isset($toggles[$slug]) && $toggles[$slug] === '1' ? '1' : '0';
            DixlaseSeoSetting::setValue($key, $value);
        }

        return redirect()
            ->route('dixlase-seo::admin.seo.integrations')
            ->with('success', __('dixlase-seo::admin/dixlase-seo/integrations.updated'));
    }

    /**
     * Bulk delete orphaned meta information
     *
     * Deletes all meta information from plugins that do not currently declare the seo-meta capability.
     */
    public function purgeOrphans(): RedirectResponse
    {
        $currentSlugs = PluginHelper::getEnabledPluginSlugsByCapability(self::REQUIRED_CAPABILITY);

        $deleted = DixlaseSeoMeta::query()
            ->when(
                ! empty($currentSlugs),
                fn ($q) => $q->whereNotIn('plugin_slug', $currentSlugs),
            )
            ->delete();

        return redirect()
            ->route('dixlase-seo::admin.seo.integrations')
            ->with('success', __('dixlase-seo::admin/dixlase-seo/integrations.orphans_purged', ['count' => $deleted]));
    }

    /**
     * Get list of active plugins that declare the seo-meta capability
     *
     * @return array<int, array{slug:string, name:string, description:string, enabled:bool}>
     */
    private function getSeoMetaPlugins(): array
    {
        $slugs = PluginHelper::getEnabledPluginSlugsByCapability(self::REQUIRED_CAPABILITY);
        if (empty($slugs)) {
            return [];
        }

        $plugins = Plugin::whereIn('slug', $slugs)->get();

        return $plugins->map(function (Plugin $plugin) {
            $key = sprintf(self::ENABLED_SETTING_KEY_FORMAT, $plugin->slug);
            $value = DixlaseSeoSetting::getValue($key, '1');

            return [
                'slug' => $plugin->slug,
                'name' => $plugin->name,
                'description' => (string) ($plugin->description ?? ''),
                'enabled' => $value !== '0' && $value !== false,
            ];
        })->values()->toArray();
    }

    /**
     * Get summary of orphaned meta information
     *
     * @param  array<int, string>  $currentSlugs  Current seo-meta compatible plugin slugs
     * @return array{count:int, byPlugin: array<int, array{plugin_slug:string, count:int}>}
     */
    private function getOrphanSummary(array $currentSlugs): array
    {
        $query = DixlaseSeoMeta::query();

        if (! empty($currentSlugs)) {
            $query->whereNotIn('plugin_slug', $currentSlugs);
        }

        $count = $query->count();

        if ($count === 0) {
            return ['count' => 0, 'byPlugin' => []];
        }

        $byPlugin = DixlaseSeoMeta::query()
            ->when(
                ! empty($currentSlugs),
                fn ($q) => $q->whereNotIn('plugin_slug', $currentSlugs),
            )
            ->selectRaw('plugin_slug, COUNT(*) as count')
            ->groupBy('plugin_slug')
            ->orderBy('plugin_slug')
            ->get()
            ->map(fn ($row) => [
                'plugin_slug' => $row->plugin_slug,
                'count' => (int) $row->count,
            ])
            ->toArray();

        return [
            'count' => $count,
            'byPlugin' => $byPlugin,
        ];
    }
}
