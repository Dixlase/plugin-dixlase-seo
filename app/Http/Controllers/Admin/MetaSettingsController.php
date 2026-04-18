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
use Plugins\DixlaseSEO\App\Http\Requests\Admin\UpdateMetaSettingsRequest;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;

/**
 * メタタグ・OGP設定の管理画面コントローラー
 *
 * - 基本設定（タイトル区切り・デフォルトディスクリプション）
 * - OGP / SNS（OGP画像・X Card・X アカウント）
 * - 組織情報（JSON-LD: 組織名・ロゴ・URL）
 */
class MetaSettingsController extends AdminLoggedInController
{
    public function __construct(
        private readonly BaseSettingRepositoryInterface $baseSettingRepository,
    ) {
        parent::__construct();
    }

    /**
     * メタタグ・OGP設定画面を表示する
     */
    public function show(): View
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
                'label' => __('dixlase-seo::admin/dixlase-seo/meta.twitter_card_summary'),
                'icon' => 'far fa-square',
                'description' => '',
            ],
            [
                'value' => 'summary_large_image',
                'label' => __('dixlase-seo::admin/dixlase-seo/meta.twitter_card_summary_large'),
                'icon' => 'far fa-image',
                'description' => '',
            ],
        ];

        return view('dixlase-seo::admin.meta', array_merge($this->viewParams, [
            'settings' => $settings,
            'twitterCardOptions' => $twitterCardOptions,
            'coreSiteDescription' => $coreSiteDescription,
            'organizationLogoMedia' => $organizationLogoMedia,
            'defaultOgpImageMedia' => $defaultOgpImageMedia,
        ]));
    }

    /**
     * メタタグ・OGP設定を更新する
     */
    public function update(UpdateMetaSettingsRequest $request): RedirectResponse
    {
        DixlaseSeoSetting::setMany($request->validated());

        return redirect()
            ->route('dixlase-seo::admin.dixlase-seo.meta')
            ->with('success', __('dixlase-seo::admin/dixlase-seo/meta.updated'));
    }
}
