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

use App\Helpers\PluginHelper;
use App\Http\Controllers\Admin\AdminLoggedInController;
use Illuminate\View\View;

/**
 * プラグイン連携設定の管理画面コントローラー
 *
 * `seo-meta` capability を宣言したプラグインを自動検出し、それぞれの
 * 有効/無効を切り替える。実際のトグル UI と保存処理はタスクCで実装予定。
 */
class IntegrationsController extends AdminLoggedInController
{
    /**
     * プラグイン連携設定画面を表示する
     */
    public function show(): View
    {
        // seo-meta capability を宣言している有効化プラグインのスラッグ一覧
        $seoMetaPluginSlugs = PluginHelper::getEnabledPluginSlugsByCapability('seo-meta');

        return view('dixlase-seo::admin.integrations', array_merge($this->viewParams, [
            'seoMetaPluginSlugs' => $seoMetaPluginSlugs,
        ]));
    }
}
