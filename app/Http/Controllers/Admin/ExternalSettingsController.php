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

use App\Http\Controllers\Admin\AdminLoggedInController;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Plugins\DixlaseSEO\App\Http\Requests\Admin\UpdateExternalSettingsRequest;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;

/**
 * 外部サービス連携設定の管理画面コントローラー
 *
 * Google Analytics、Google Search Console等の外部サービスとの連携情報を管理。
 */
class ExternalSettingsController extends AdminLoggedInController
{
    /**
     * 外部サービス連携設定画面を表示する
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
     * 外部サービス連携設定を更新する
     */
    public function update(UpdateExternalSettingsRequest $request): RedirectResponse
    {
        DixlaseSeoSetting::setMany($request->validated());

        return redirect()
            ->route('dixlase-seo::admin.seo.external')
            ->with('success', __('dixlase-seo::admin/dixlase-seo/external.updated'));
    }
}
