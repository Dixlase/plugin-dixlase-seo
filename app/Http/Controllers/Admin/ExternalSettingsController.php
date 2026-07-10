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

use App\Contracts\Cookie\ConsentStateProviderInterface;
use App\Helpers\AdminHelper;
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
     * PermissionRegistry resolves plugin roles.php via the PascalCase
     * directory basename, so this must be the directory name.
     */
    private const PLUGIN_SLUG = 'DixlaseSEO';

    /**
     * Display the external service integration settings screen
     */
    public function show(): View
    {
        $this->authorizeView('dixlase-seo.external');

        $defaults = config('dixlase_seo', []);
        $settings = DixlaseSeoSetting::getMany($defaults);

        return view('dixlase-seo::admin.external', array_merge($this->viewParams, [
            'settings' => $settings,
            'consentStatus' => $this->consentStatus(),
        ]));
    }

    /**
     * Report the cookie-consent banner status for the analytics notice.
     *
     * Detection is fully generic: ANY plugin (official or third-party) that
     * binds the core App\Contracts\Cookie\ConsentStateProviderInterface is
     * recognised as a consent system. Whether its banner is actively shown is
     * read from the optional isBannerEnabled() probe (duck-typed, so a
     * provider that does not expose it is assumed active).
     *
     *   - 'active'        — a consent banner is up; GA is gated on consent.
     *   - 'banner_off'    — a consent provider exists but its banner is off.
     *   - 'not_installed' — no consent provider at all.
     */
    private function consentStatus(): string
    {
        if (! app()->bound(ConsentStateProviderInterface::class)) {
            return 'not_installed';
        }

        $provider = app(ConsentStateProviderInterface::class);

        $bannerActive = ! method_exists($provider, 'isBannerEnabled')
            || $provider->isBannerEnabled();

        return $bannerActive ? 'active' : 'banner_off';
    }

    /**
     * Update the external service integration settings
     */
    public function update(UpdateExternalSettingsRequest $request): RedirectResponse
    {
        $this->authorizeEdit('dixlase-seo.external');

        // The form-toggle component ships a hidden "0" field alongside the
        // checkbox "1", so the request already carries the correct boolean
        // value — no $request->has() normalisation (which only checks
        // presence, not value, and would always resolve to "1").
        DixlaseSeoSetting::setMany($request->validated());

        return redirect()
            ->route('dixlase-seo::admin.seo.external')
            ->with('success', __('dixlase-seo::admin/dixlase-seo/external.updated'));
    }

    /**
     * Abort with 403 unless the current member can VIEW the given menu.
     * SUPER_ADMIN bypasses; core role_permission_overrides and the plugin
     * defaults in config/admin/roles.php are honoured via AdminHelper.
     */
    private function authorizeView(string $menuKey): void
    {
        if (! AdminHelper::canViewPluginMenu(self::PLUGIN_SLUG, $menuKey)) {
            abort(403, __('http/middleware/check_menu_access.no_access_permission'));
        }
    }

    /**
     * Abort with 403 unless the current member can EDIT the given menu.
     */
    private function authorizeEdit(string $menuKey): void
    {
        if (! AdminHelper::canEditPluginMenu(self::PLUGIN_SLUG, $menuKey)) {
            abort(403, __('http/middleware/check_menu_edit.no_edit_permission'));
        }
    }
}
