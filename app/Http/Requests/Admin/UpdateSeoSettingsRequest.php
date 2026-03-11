<?php

/**
 * This file is part of DixlaseSEO.
 *
 * Copyright (C) 2026
 * https://example.com
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

namespace Plugins\DixlaseSEO\App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * SEO設定更新リクエストのバリデーション
 */
class UpdateSeoSettingsRequest extends FormRequest
{
    /**
     * リクエストの認可判定
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーションルール
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title_separator' => ['required', 'string', 'max:10'],
            'default_description' => ['nullable', 'string', 'max:300'],
            'default_ogp_image' => ['nullable', 'string', 'max:500'],
            'twitter_card_type' => ['required', Rule::in(['summary', 'summary_large_image'])],
            'twitter_site' => ['nullable', 'string', 'max:100'],
            'organization_name' => ['nullable', 'string', 'max:200'],
            'organization_logo' => ['nullable', 'string', 'max:500'],
            'organization_url' => ['nullable', 'url', 'max:500'],
            'sitemap_enabled' => ['nullable'],
            'sitemap_changefreq' => ['required', Rule::in(['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'])],
            'sitemap_priority' => ['required', 'numeric', 'min:0', 'max:1'],
            'robots_txt_mode' => ['required', Rule::in(['auto', 'custom'])],
            'robots_txt' => ['nullable', 'string', 'max:10000'],
        ];
    }
}
