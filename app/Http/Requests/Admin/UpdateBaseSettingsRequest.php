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

namespace Plugins\DixlaseSEO\App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for SEO base settings update request
 */
class UpdateBaseSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title_separator' => ['required', 'string', 'max:10'],
            'default_title' => ['nullable', 'string', 'max:100'],
            'default_description' => ['nullable', 'string', 'max:300'],
            'default_ogp_image' => ['nullable', 'integer', 'exists:media,id'],
            'twitter_card_type' => ['required', Rule::in(['summary', 'summary_large_image'])],
            'twitter_site' => ['nullable', 'string', 'max:100'],
            'organization_name' => ['nullable', 'string', 'max:200'],
            'organization_logo' => ['nullable', 'integer', 'exists:media,id'],
            'organization_url' => ['nullable', 'url', 'max:500'],
        ];
    }
}
