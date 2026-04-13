<?php

/**
 * This file is part of Dixlase SEO.
 *
 * Copyright (C) 2026 exc-D inc.
 * https://exc-d.com
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

use App\Contracts\Repositories\BaseSettingRepositoryInterface;
use App\Http\Controllers\Admin\AdminLoggedInController;
use App\Models\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Plugins\DixlaseSEO\App\Http\Requests\Admin\UpdateSeoSettingsRequest;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;

/**
 * SEO設定の管理画面コントローラー
 */
class DixlaseSeoAdminSettingsController extends AdminLoggedInController
{
    /**
     * コンストラクタ
     */
    public function __construct(
        private readonly BaseSettingRepositoryInterface $baseSettingRepository,
    ) {
        parent::__construct();
    }

    /**
     * SEO設定画面を表示する
     */
    public function settings(): View
    {
        $defaults = config('dixlase_seo', []);
        $settings = DixlaseSeoSetting::getMany($defaults);

        // コアのサイト説明をフォールバックとして提供
        $coreSiteDescription = (string) $this->baseSettingRepository->get('site_description', '');

        // 組織ロゴ: メディアIDからMediaモデルを解決
        $organizationLogoMedia = null;
        if (! empty($settings['organization_logo'])) {
            $organizationLogoMedia = Media::find($settings['organization_logo']);
        }

        // デフォルトOGP画像: メディアIDからMediaモデルを解決
        $defaultOgpImageMedia = null;
        if (! empty($settings['default_ogp_image'])) {
            $defaultOgpImageMedia = Media::find($settings['default_ogp_image']);
        }

        // X（旧Twitter）Cardタイプの選択肢
        $twitterCardOptions = [
            [
                'value' => 'summary',
                'label' => __('dixlase-seo::admin/dixlase-seo/settings.twitter_card_summary'),
                'icon' => 'far fa-square',
                'description' => '',
            ],
            [
                'value' => 'summary_large_image',
                'label' => __('dixlase-seo::admin/dixlase-seo/settings.twitter_card_summary_large'),
                'icon' => 'far fa-image',
                'description' => '',
            ],
        ];

        // robots.txtモードの選択肢
        $robotsTxtModeOptions = [
            [
                'value' => 'auto',
                'label' => __('dixlase-seo::admin/dixlase-seo/settings.robots_txt_mode_auto'),
                'icon' => 'fas fa-magic',
                'description' => __('dixlase-seo::admin/dixlase-seo/settings.robots_txt_mode_auto_desc'),
            ],
            [
                'value' => 'custom',
                'label' => __('dixlase-seo::admin/dixlase-seo/settings.robots_txt_mode_custom'),
                'icon' => 'fas fa-edit',
                'description' => __('dixlase-seo::admin/dixlase-seo/settings.robots_txt_mode_custom_desc'),
            ],
        ];

        // 更新頻度の選択肢
        $changefreqOptions = [
            'always' => __('dixlase-seo::admin/dixlase-seo/settings.changefreq_always'),
            'hourly' => __('dixlase-seo::admin/dixlase-seo/settings.changefreq_hourly'),
            'daily' => __('dixlase-seo::admin/dixlase-seo/settings.changefreq_daily'),
            'weekly' => __('dixlase-seo::admin/dixlase-seo/settings.changefreq_weekly'),
            'monthly' => __('dixlase-seo::admin/dixlase-seo/settings.changefreq_monthly'),
            'yearly' => __('dixlase-seo::admin/dixlase-seo/settings.changefreq_yearly'),
            'never' => __('dixlase-seo::admin/dixlase-seo/settings.changefreq_never'),
        ];

        return view('dixlase-seo::admin.settings.index', array_merge($this->viewParams, [
            'settings' => $settings,
            'twitterCardOptions' => $twitterCardOptions,
            'robotsTxtModeOptions' => $robotsTxtModeOptions,
            'changefreqOptions' => $changefreqOptions,
            'coreSiteDescription' => $coreSiteDescription,
            'organizationLogoMedia' => $organizationLogoMedia,
            'defaultOgpImageMedia' => $defaultOgpImageMedia,
        ]));
    }

    /**
     * SEO設定を更新する
     */
    public function updateSettings(UpdateSeoSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // チェックボックス（トグル）はチェックされていないとリクエストに含まれないため処理
        $validated['sitemap_enabled'] = $request->has('sitemap_enabled') ? '1' : '0';

        DixlaseSeoSetting::setMany($validated);

        return redirect()
            ->route('dixlase-seo::admin.dixlase-seo.settings')
            ->with('success', __('dixlase-seo::admin/dixlase-seo/settings.updated'));
    }
}
