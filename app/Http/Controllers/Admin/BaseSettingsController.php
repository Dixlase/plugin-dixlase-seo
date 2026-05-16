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

use App\Contracts\Repositories\MediaRepositoryInterface;
use App\Contracts\Repositories\SiteSettingRepositoryInterface;
use App\Http\Controllers\Admin\AdminLoggedInController;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Plugins\DixlaseSEO\App\Http\Requests\Admin\UpdateBaseSettingsRequest;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;

/**
 * Admin panel controller for SEO basic settings
 *
 * - Title separator, default description
 * - OGP / SNS (OGP image, X Card, X account)
 * - Organization info (JSON-LD: organization name, logo, URL)
 */
class BaseSettingsController extends AdminLoggedInController
{
    public function __construct(
        private readonly SiteSettingRepositoryInterface $baseSettingRepository,
        private readonly MediaRepositoryInterface $mediaRepository,
    ) {
        parent::__construct();
    }

    /**
     * Display SEO basic settings screen
     */
    public function show(): View
    {
        $defaults = config('dixlase_seo', []);
        $settings = DixlaseSeoSetting::getMany($defaults);

        // Provide Core site description as fallback
        $coreSiteDescription = (string) $this->baseSettingRepository->get('site_description', '');

        // Organization logo: resolve Media via core repository contract
        $organizationLogoMedia = null;
        if (! empty($settings['organization_logo'])) {
            $organizationLogoMedia = $this->mediaRepository->find((int) $settings['organization_logo']);
        }

        // Default OGP image: resolve Media via core repository contract
        $defaultOgpImageMedia = null;
        if (! empty($settings['default_ogp_image'])) {
            $defaultOgpImageMedia = $this->mediaRepository->find((int) $settings['default_ogp_image']);
        }

        // X (formerly Twitter) Card type options
        $twitterCardOptions = [
            [
                'value' => 'summary',
                'label' => __('dixlase-seo::admin/dixlase-seo/base.twitter_card_summary'),
                'icon' => 'far fa-square',
                'description' => '',
            ],
            [
                'value' => 'summary_large_image',
                'label' => __('dixlase-seo::admin/dixlase-seo/base.twitter_card_summary_large'),
                'icon' => 'far fa-image',
                'description' => '',
            ],
        ];

        return view('dixlase-seo::admin.base', array_merge($this->viewParams, [
            'settings' => $settings,
            'twitterCardOptions' => $twitterCardOptions,
            'coreSiteDescription' => $coreSiteDescription,
            'organizationLogoMedia' => $organizationLogoMedia,
            'defaultOgpImageMedia' => $defaultOgpImageMedia,
        ]));
    }

    /**
     * Update SEO basic settings
     */
    public function update(UpdateBaseSettingsRequest $request): RedirectResponse
    {
        DixlaseSeoSetting::setMany($request->validated());

        return redirect()
            ->route('dixlase-seo::admin.seo.base')
            ->with('success', __('dixlase-seo::admin/dixlase-seo/base.updated'));
    }
}
