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
<form id="dixlase-seo-sitemap-form" method="POST" action="{{ route('dixlase-seo::admin.seo.sitemap.update') }}" x-data="{ robotsTxtMode: '{{ old('robots_txt_mode', $settings['robots_txt_mode'] ?? 'auto') }}' }">
    @csrf
    @method('PATCH')

    {{-- サイトマップ --}}
    <section class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-sitemap mr-2"></i>{{ __('dixlase-seo::admin/dixlase-seo/sitemap.section_sitemap') }}
        </h2>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 space-y-6">
            <div>
                <x-form-toggle
                    name="sitemap_enabled"
                    :label="__('dixlase-seo::admin/dixlase-seo/sitemap.sitemap_enabled')"
                    :checked="old('sitemap_enabled', $settings['sitemap_enabled'] ?? true)"
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/sitemap.sitemap_enabled_help')" />
            </div>

            <div>
                <x-form-label for="sitemap_changefreq" :text="__('dixlase-seo::admin/dixlase-seo/sitemap.sitemap_changefreq')" class="mb-1" />
                <x-form-select
                    name="sitemap_changefreq"
                    :options="$changefreqOptions"
                    :value="old('sitemap_changefreq', $settings['sitemap_changefreq'] ?? 'weekly')"
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/sitemap.sitemap_changefreq_help')" />
            </div>

            <div>
                <x-form-label for="sitemap_priority" :text="__('dixlase-seo::admin/dixlase-seo/sitemap.sitemap_priority')" class="mb-1" />
                <x-form-text
                    name="sitemap_priority"
                    :value="old('sitemap_priority', $settings['sitemap_priority'] ?? '0.5')"
                    type="number"
                    step="0.1"
                    min="0"
                    max="1"
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/sitemap.sitemap_priority_help')" />
            </div>
        </div>
    </section>

    {{-- robots.txt --}}
    <section class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-robot mr-2"></i>{{ __('dixlase-seo::admin/dixlase-seo/sitemap.section_robots') }}
        </h2>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 space-y-6">
            <div>
                <x-form-label for="robots_txt_mode" :text="__('dixlase-seo::admin/dixlase-seo/sitemap.robots_txt_mode')" class="mb-1" />
                <x-form-radio-card-group
                    name="robots_txt_mode"
                    :options="$robotsTxtModeOptions"
                    :value="old('robots_txt_mode', $settings['robots_txt_mode'] ?? 'auto')"
                    :columns="2"
                    xModel="robotsTxtMode"
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/sitemap.robots_txt_mode_help')" />
            </div>

            <div x-show="robotsTxtMode === 'custom'" x-cloak>
                <x-form-label for="robots_txt" :text="__('dixlase-seo::admin/dixlase-seo/sitemap.robots_txt_content')" class="mb-1" />
                <x-form-textarea
                    name="robots_txt"
                    :value="old('robots_txt', $settings['robots_txt'] ?? '')"
                    rows="8"
                    class="font-mono text-sm"
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/sitemap.robots_txt_content_help')" />
            </div>
        </div>
    </section>
</form>
@endsection

@section('save')
    <x-admin.save-button form="dixlase-seo-sitemap-form" />
@endsection
