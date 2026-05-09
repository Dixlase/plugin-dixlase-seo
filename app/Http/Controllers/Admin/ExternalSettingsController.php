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

use App\Http\Controllers\Admin\AdminLoggedInController;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Plugins\DixlaseSEO\App\Http\Requests\Admin\UpdateExternalSettingsRequest;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;

/**
 * Admin panel controller for external service integration settings
 *
 * Manages integration information with external services such as Google Analytics, Google Search Console, etc.
 */
class ExternalSettingsController extends AdminLoggedInController
{
    /**
     * Display the external service integration settings screen
     */
    public function show(): View
    {
        $defaults = config('dixlase_seo', []);
        $settings = DixlaseSeoSetting::getMany($defaults);

        return view('dixlase-seo::admin.external', array_merge($this->viewParams, [
            'settings' => $settings,
        ]));
    }

    /**
     * Update the external service integration settings
     */
    public function update(UpdateExternalSettingsRequest $request): RedirectResponse
    {
        DixlaseSeoSetting::setMany($request->validated());

        return redirect()
            ->route('dixlase-seo::admin.seo.external')
            ->with('success', __('dixlase-seo::admin/dixlase-seo/external.updated'));
    }
}
