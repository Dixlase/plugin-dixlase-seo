{{--
  This file is part of DixlaseSEO.

  Copyright (C) 2026
  https://example.com

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
<form method="POST" action="{{ route('dixlase-seo::admin.dixlase-seo.settings.update') }}" x-data="{ robotsTxtMode: '{{ old('robots_txt_mode', $settings['robots_txt_mode'] ?? 'auto') }}' }">
    @csrf
    @method('PATCH')

    {{-- 基本設定 --}}
    <section class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-cog mr-2"></i>{{ __('dixlase-seo::admin/dixlase-seo/settings.section_general') }}
        </h2>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 space-y-6">
            <x-form-text
                name="title_separator"
                :label="__('dixlase-seo::admin/dixlase-seo/settings.title_separator')"
                :value="old('title_separator', $settings['title_separator'] ?? '|')"
                :help="__('dixlase-seo::admin/dixlase-seo/settings.title_separator_help')"
                required
            />

            <x-form-textarea
                name="default_description"
                :label="__('dixlase-seo::admin/dixlase-seo/settings.default_description')"
                :value="old('default_description', $settings['default_description'] ?? '')"
                :help="__('dixlase-seo::admin/dixlase-seo/settings.default_description_help')"
                rows="3"
            />
        </div>
    </section>

    {{-- OGP / SNS設定 --}}
    <section class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-share-alt mr-2"></i>{{ __('dixlase-seo::admin/dixlase-seo/settings.section_ogp') }}
        </h2>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 space-y-6">
            <x-media.picker
                name="default_ogp_image"
                :label="__('dixlase-seo::admin/dixlase-seo/settings.default_ogp_image')"
                :value="old('default_ogp_image', $settings['default_ogp_image'] ?? '')"
                :help="__('dixlase-seo::admin/dixlase-seo/settings.default_ogp_image_help')"
                aspectRatio="ogp"
            />

            <x-form-radio-card-group
                name="twitter_card_type"
                :label="__('dixlase-seo::admin/dixlase-seo/settings.twitter_card_type')"
                :options="$twitterCardOptions"
                :value="old('twitter_card_type', $settings['twitter_card_type'] ?? 'summary_large_image')"
                :help="__('dixlase-seo::admin/dixlase-seo/settings.twitter_card_type_help')"
                :columns="2"
            />

            <x-form-text
                name="twitter_site"
                :label="__('dixlase-seo::admin/dixlase-seo/settings.twitter_site')"
                :value="old('twitter_site', $settings['twitter_site'] ?? '')"
                :help="__('dixlase-seo::admin/dixlase-seo/settings.twitter_site_help')"
                placeholder="username"
            />
        </div>
    </section>

    {{-- 組織情報（JSON-LD） --}}
    <section class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-building mr-2"></i>{{ __('dixlase-seo::admin/dixlase-seo/settings.section_organization') }}
        </h2>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 space-y-6">
            <x-form-text
                name="organization_name"
                :label="__('dixlase-seo::admin/dixlase-seo/settings.organization_name')"
                :value="old('organization_name', $settings['organization_name'] ?? '')"
                :help="__('dixlase-seo::admin/dixlase-seo/settings.organization_name_help')"
            />

            <x-form-text
                name="organization_logo"
                :label="__('dixlase-seo::admin/dixlase-seo/settings.organization_logo')"
                :value="old('organization_logo', $settings['organization_logo'] ?? '')"
                :help="__('dixlase-seo::admin/dixlase-seo/settings.organization_logo_help')"
                placeholder="https://example.com/logo.png"
            />

            <x-form-text
                name="organization_url"
                :label="__('dixlase-seo::admin/dixlase-seo/settings.organization_url')"
                :value="old('organization_url', $settings['organization_url'] ?? '')"
                :help="__('dixlase-seo::admin/dixlase-seo/settings.organization_url_help')"
                placeholder="https://example.com"
            />
        </div>
    </section>

    {{-- サイトマップ --}}
    <section class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-sitemap mr-2"></i>{{ __('dixlase-seo::admin/dixlase-seo/settings.section_sitemap') }}
        </h2>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 space-y-6">
            <x-form-toggle
                name="sitemap_enabled"
                :label="__('dixlase-seo::admin/dixlase-seo/settings.sitemap_enabled')"
                :checked="old('sitemap_enabled', $settings['sitemap_enabled'] ?? true)"
                :help="__('dixlase-seo::admin/dixlase-seo/settings.sitemap_enabled_help')"
            />

            <x-form-select
                name="sitemap_changefreq"
                :label="__('dixlase-seo::admin/dixlase-seo/settings.sitemap_changefreq')"
                :options="$changefreqOptions"
                :value="old('sitemap_changefreq', $settings['sitemap_changefreq'] ?? 'weekly')"
                :help="__('dixlase-seo::admin/dixlase-seo/settings.sitemap_changefreq_help')"
            />

            <x-form-text
                name="sitemap_priority"
                :label="__('dixlase-seo::admin/dixlase-seo/settings.sitemap_priority')"
                :value="old('sitemap_priority', $settings['sitemap_priority'] ?? '0.5')"
                :help="__('dixlase-seo::admin/dixlase-seo/settings.sitemap_priority_help')"
                type="number"
                step="0.1"
                min="0"
                max="1"
            />
        </div>
    </section>

    {{-- robots.txt --}}
    <section class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-robot mr-2"></i>{{ __('dixlase-seo::admin/dixlase-seo/settings.section_robots') }}
        </h2>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 space-y-6">
            <x-form-radio-card-group
                name="robots_txt_mode"
                :label="__('dixlase-seo::admin/dixlase-seo/settings.robots_txt_mode')"
                :options="$robotsTxtModeOptions"
                :value="old('robots_txt_mode', $settings['robots_txt_mode'] ?? 'auto')"
                :help="__('dixlase-seo::admin/dixlase-seo/settings.robots_txt_mode_help')"
                :columns="2"
                xModel="robotsTxtMode"
            />

            <div x-show="robotsTxtMode === 'custom'" x-cloak>
                <x-form-textarea
                    name="robots_txt"
                    :label="__('dixlase-seo::admin/dixlase-seo/settings.robots_txt_content')"
                    :value="old('robots_txt', $settings['robots_txt'] ?? '')"
                    :help="__('dixlase-seo::admin/dixlase-seo/settings.robots_txt_content_help')"
                    rows="8"
                    class="font-mono text-sm"
                />
            </div>
        </div>
    </section>
</form>
@endsection

@section('save')
    <x-admin.save-button form="true" />
@endsection
