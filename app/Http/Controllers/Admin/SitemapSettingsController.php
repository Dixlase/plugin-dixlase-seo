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

namespace Plugins\DixlaseSEO\App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminLoggedInController;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Plugins\DixlaseSEO\App\Http\Requests\Admin\UpdateSitemapSettingsRequest;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;

/**
 * Admin panel controller for sitemap and robots.txt settings
 */
class SitemapSettingsController extends AdminLoggedInController
{
    /**
     * Display the sitemap and robots.txt settings screen
     */
    public function show(): View
    {
        $defaults = config('dixlase_seo', []);
        $settings = DixlaseSeoSetting::getMany($defaults);

        // Options for robots.txt mode
        $robotsTxtModeOptions = [
            [
                'value' => 'auto',
                'label' => __('dixlase-seo::admin/dixlase-seo/sitemap.robots_txt_mode_auto'),
                'icon' => 'fas fa-magic',
                'description' => __('dixlase-seo::admin/dixlase-seo/sitemap.robots_txt_mode_auto_desc'),
            ],
            [
                'value' => 'custom',
                'label' => __('dixlase-seo::admin/dixlase-seo/sitemap.robots_txt_mode_custom'),
                'icon' => 'fas fa-edit',
                'description' => __('dixlase-seo::admin/dixlase-seo/sitemap.robots_txt_mode_custom_desc'),
            ],
        ];

        // Options for update frequency
        $changefreqOptions = [
            'always' => __('dixlase-seo::admin/dixlase-seo/sitemap.changefreq_always'),
            'hourly' => __('dixlase-seo::admin/dixlase-seo/sitemap.changefreq_hourly'),
            'daily' => __('dixlase-seo::admin/dixlase-seo/sitemap.changefreq_daily'),
            'weekly' => __('dixlase-seo::admin/dixlase-seo/sitemap.changefreq_weekly'),
            'monthly' => __('dixlase-seo::admin/dixlase-seo/sitemap.changefreq_monthly'),
            'yearly' => __('dixlase-seo::admin/dixlase-seo/sitemap.changefreq_yearly'),
            'never' => __('dixlase-seo::admin/dixlase-seo/sitemap.changefreq_never'),
        ];

        return view('dixlase-seo::admin.sitemap', array_merge($this->viewParams, [
            'settings' => $settings,
            'robotsTxtModeOptions' => $robotsTxtModeOptions,
            'changefreqOptions' => $changefreqOptions,
        ]));
    }

    /**
     * Update sitemap and robots.txt settings
     */
    public function update(UpdateSitemapSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Process checkboxes (toggles) since unchecked ones are not included in the request
        $validated['sitemap_enabled'] = $request->has('sitemap_enabled') ? '1' : '0';

        DixlaseSeoSetting::setMany($validated);

        return redirect()
            ->route('dixlase-seo::admin.seo.sitemap')
            ->with('success', __('dixlase-seo::admin/dixlase-seo/sitemap.updated'));
    }
}
