{{--
This file is part of Dixlase SEO.

Copyright (C) 2026 exc-D inc.
https://exc-d.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <https://www.gnu.org/licenses/>.
--}}

@extends('layouts.admin')

@section('content')
<form id="dixlase-seo-base-form" method="POST" action="{{ route('dixlase-seo::admin.seo.base.update') }}">
    @csrf
    @method('PATCH')

    {{-- 基本設定 --}}
    <section class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-cog mr-2"></i>{{ __('dixlase-seo::admin/dixlase-seo/base.section_general') }}
        </h2>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 space-y-6">
            <div>
                <x-form-label for="title_separator" :text="__('dixlase-seo::admin/dixlase-seo/base.title_separator')" :required="true" class="mb-1" />
                <x-form-text
                    name="title_separator"
                    :value="old('title_separator', $settings['title_separator'] ?? '|')"
                    required
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/base.title_separator_help')" />
            </div>

            <div>
                <x-form-label for="default_description" :text="__('dixlase-seo::admin/dixlase-seo/base.default_description')" class="mb-1" />
                <x-form-textarea
                    name="default_description"
                    :value="old('default_description', $settings['default_description'] ?? '')"
                    :placeholder="$coreSiteDescription"
                    rows="3"
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/base.default_description_help')" />
            </div>
        </div>
    </section>

    {{-- OGP / SNS設定 --}}
    <section class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-share-alt mr-2"></i>{{ __('dixlase-seo::admin/dixlase-seo/base.section_ogp') }}
        </h2>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 space-y-6">
            <div>
                <x-form-label for="default_ogp_image" :text="__('dixlase-seo::admin/dixlase-seo/base.default_ogp_image')" class="mb-1" />
                <x-media.picker
                    name="default_ogp_image"
                    :value="old('default_ogp_image', $settings['default_ogp_image'] ?? '')"
                    :media="$defaultOgpImageMedia"
                    aspectRatio="ogp"
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/base.default_ogp_image_help')" />
            </div>

            <div>
                <x-form-label for="twitter_card_type" :text="__('dixlase-seo::admin/dixlase-seo/base.twitter_card_type')" class="mb-1" />
                <x-form-radio-card-group
                    name="twitter_card_type"
                    :options="$twitterCardOptions"
                    :value="old('twitter_card_type', $settings['twitter_card_type'] ?? 'summary_large_image')"
                    :columns="2"
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/base.twitter_card_type_help')" />
            </div>

            <div>
                <x-form-label for="twitter_site" :text="__('dixlase-seo::admin/dixlase-seo/base.twitter_site')" class="mb-1" />
                <x-form-text
                    name="twitter_site"
                    :value="old('twitter_site', $settings['twitter_site'] ?? '')"
                    placeholder="username"
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/base.twitter_site_help')" />
            </div>
        </div>
    </section>

    {{-- 組織情報（JSON-LD） --}}
    <section class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-building mr-2"></i>{{ __('dixlase-seo::admin/dixlase-seo/base.section_organization') }}
        </h2>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 space-y-6">
            <div>
                <x-form-label for="organization_name" :text="__('dixlase-seo::admin/dixlase-seo/base.organization_name')" class="mb-1" />
                <x-form-text
                    name="organization_name"
                    :value="old('organization_name', $settings['organization_name'] ?? '')"
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/base.organization_name_help')" />
            </div>

            <div>
                <x-form-label for="organization_logo" :text="__('dixlase-seo::admin/dixlase-seo/base.organization_logo')" class="mb-1" />
                <x-media.picker
                    name="organization_logo"
                    :value="old('organization_logo', $settings['organization_logo'] ?? '')"
                    :media="$organizationLogoMedia"
                    aspectRatio="square"
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/base.organization_logo_help')" />
            </div>

            <div>
                <x-form-label for="organization_url" :text="__('dixlase-seo::admin/dixlase-seo/base.organization_url')" class="mb-1" />
                <x-form-text
                    name="organization_url"
                    :value="old('organization_url', $settings['organization_url'] ?? '')"
                    placeholder="https://example.com"
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/base.organization_url_help')" />
            </div>
        </div>
    </section>
</form>
@endsection

@section('save')
    <x-admin.save-button form="dixlase-seo-base-form" />
@endsection
