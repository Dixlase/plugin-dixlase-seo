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
            <div>
                <x-form-label for="title_separator" :text="__('dixlase-seo::admin/dixlase-seo/settings.title_separator')" :required="true" class="mb-1" />
                <x-form-text
                    name="title_separator"
                    :value="old('title_separator', $settings['title_separator'] ?? '|')"
                    required
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/settings.title_separator_help')" />
            </div>

            <div>
                <x-form-label for="default_description" :text="__('dixlase-seo::admin/dixlase-seo/settings.default_description')" class="mb-1" />
                <x-form-textarea
                    name="default_description"
                    :value="old('default_description', $settings['default_description'] ?? '')"
                    rows="3"
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/settings.default_description_help')" />
            </div>
        </div>
    </section>

    {{-- OGP / SNS設定 --}}
    <section class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-share-alt mr-2"></i>{{ __('dixlase-seo::admin/dixlase-seo/settings.section_ogp') }}
        </h2>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 space-y-6">
            <div>
                <x-media.picker
                    name="default_ogp_image"
                    :label="__('dixlase-seo::admin/dixlase-seo/settings.default_ogp_image')"
                    :value="old('default_ogp_image', $settings['default_ogp_image'] ?? '')"
                    :help="__('dixlase-seo::admin/dixlase-seo/settings.default_ogp_image_help')"
                    aspectRatio="ogp"
                />
            </div>

            <div>
                <x-form-label for="twitter_card_type" :text="__('dixlase-seo::admin/dixlase-seo/settings.twitter_card_type')" class="mb-1" />
                <x-form-radio-card-group
                    name="twitter_card_type"
                    :options="$twitterCardOptions"
                    :value="old('twitter_card_type', $settings['twitter_card_type'] ?? 'summary_large_image')"
                    :columns="2"
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/settings.twitter_card_type_help')" />
            </div>

            <div>
                <x-form-label for="twitter_site" :text="__('dixlase-seo::admin/dixlase-seo/settings.twitter_site')" class="mb-1" />
                <x-form-text
                    name="twitter_site"
                    :value="old('twitter_site', $settings['twitter_site'] ?? '')"
                    placeholder="username"
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/settings.twitter_site_help')" />
            </div>
        </div>
    </section>

    {{-- 組織情報（JSON-LD） --}}
    <section class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-building mr-2"></i>{{ __('dixlase-seo::admin/dixlase-seo/settings.section_organization') }}
        </h2>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 space-y-6">
            <div>
                <x-form-label for="organization_name" :text="__('dixlase-seo::admin/dixlase-seo/settings.organization_name')" class="mb-1" />
                <x-form-text
                    name="organization_name"
                    :value="old('organization_name', $settings['organization_name'] ?? '')"
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/settings.organization_name_help')" />
            </div>

            <div>
                <x-form-label for="organization_logo" :text="__('dixlase-seo::admin/dixlase-seo/settings.organization_logo')" class="mb-1" />
                <x-form-text
                    name="organization_logo"
                    :value="old('organization_logo', $settings['organization_logo'] ?? '')"
                    placeholder="https://example.com/logo.png"
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/settings.organization_logo_help')" />
            </div>

            <div>
                <x-form-label for="organization_url" :text="__('dixlase-seo::admin/dixlase-seo/settings.organization_url')" class="mb-1" />
                <x-form-text
                    name="organization_url"
                    :value="old('organization_url', $settings['organization_url'] ?? '')"
                    placeholder="https://example.com"
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/settings.organization_url_help')" />
            </div>
        </div>
    </section>

    {{-- サイトマップ --}}
    <section class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-sitemap mr-2"></i>{{ __('dixlase-seo::admin/dixlase-seo/settings.section_sitemap') }}
        </h2>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 space-y-6">
            <div>
                <x-form-toggle
                    name="sitemap_enabled"
                    :label="__('dixlase-seo::admin/dixlase-seo/settings.sitemap_enabled')"
                    :checked="old('sitemap_enabled', $settings['sitemap_enabled'] ?? true)"
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/settings.sitemap_enabled_help')" />
            </div>

            <div>
                <x-form-label for="sitemap_changefreq" :text="__('dixlase-seo::admin/dixlase-seo/settings.sitemap_changefreq')" class="mb-1" />
                <x-form-select
                    name="sitemap_changefreq"
                    :options="$changefreqOptions"
                    :value="old('sitemap_changefreq', $settings['sitemap_changefreq'] ?? 'weekly')"
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/settings.sitemap_changefreq_help')" />
            </div>

            <div>
                <x-form-label for="sitemap_priority" :text="__('dixlase-seo::admin/dixlase-seo/settings.sitemap_priority')" class="mb-1" />
                <x-form-text
                    name="sitemap_priority"
                    :value="old('sitemap_priority', $settings['sitemap_priority'] ?? '0.5')"
                    type="number"
                    step="0.1"
                    min="0"
                    max="1"
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/settings.sitemap_priority_help')" />
            </div>
        </div>
    </section>

    {{-- robots.txt --}}
    <section class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-robot mr-2"></i>{{ __('dixlase-seo::admin/dixlase-seo/settings.section_robots') }}
        </h2>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 space-y-6">
            <div>
                <x-form-label for="robots_txt_mode" :text="__('dixlase-seo::admin/dixlase-seo/settings.robots_txt_mode')" class="mb-1" />
                <x-form-radio-card-group
                    name="robots_txt_mode"
                    :options="$robotsTxtModeOptions"
                    :value="old('robots_txt_mode', $settings['robots_txt_mode'] ?? 'auto')"
                    :columns="2"
                    xModel="robotsTxtMode"
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/settings.robots_txt_mode_help')" />
            </div>

            <div x-show="robotsTxtMode === 'custom'" x-cloak>
                <x-form-label for="robots_txt" :text="__('dixlase-seo::admin/dixlase-seo/settings.robots_txt_content')" class="mb-1" />
                <x-form-textarea
                    name="robots_txt"
                    :value="old('robots_txt', $settings['robots_txt'] ?? '')"
                    rows="8"
                    class="font-mono text-sm"
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/settings.robots_txt_content_help')" />
            </div>
        </div>
    </section>

    {{-- 外部サービス連携 --}}
    <section class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-plug mr-2"></i>{{ __('dixlase-seo::admin/dixlase-seo/settings.section_external') }}
        </h2>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 space-y-6">
            <div>
                <x-form-label for="google_analytics_id" :text="__('dixlase-seo::admin/dixlase-seo/settings.google_analytics_id')" class="mb-1" />
                <x-form-text
                    name="google_analytics_id"
                    :value="old('google_analytics_id', $settings['google_analytics_id'] ?? '')"
                    :placeholder="__('dixlase-seo::admin/dixlase-seo/settings.google_analytics_id_placeholder')"
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/settings.google_analytics_id_help')" />
            </div>

            <div>
                <x-form-label for="google_site_verification" :text="__('dixlase-seo::admin/dixlase-seo/settings.google_site_verification')" class="mb-1" />
                <x-form-text
                    name="google_site_verification"
                    :value="old('google_site_verification', $settings['google_site_verification'] ?? '')"
                    :placeholder="__('dixlase-seo::admin/dixlase-seo/settings.google_site_verification_placeholder')"
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/settings.google_site_verification_help')" />
            </div>
        </div>
    </section>
</form>
@endsection

@section('save')
    <x-admin.save-button form="true" />
@endsection
