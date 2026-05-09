{{--
This file is part of Dixlase SEO.

Copyright (C) 2026 exc-D inc.
https://exc-d.com

Dixlase SEO is dual-licensed. You may use this file under either:

  (a) the GNU General Public License version 3 or later, as published
      by the Free Software Foundation; or

  (b) a commercial license agreement obtained from exc-D inc.

Unless you have entered into a commercial license agreement, this
file is governed by the GPL terms below.

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
{{-- Plugin list + toggle --}}
<section class="mb-8">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
        <i class="fas fa-puzzle-piece mr-2"></i>{{ __('dixlase-seo::admin/dixlase-seo/integrations.section_plugins') }}
    </h2>

    @if (empty($plugins))
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-gray-400 dark:text-gray-500 mt-1 mr-3"></i>
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">
                        {{ __('dixlase-seo::admin/dixlase-seo/integrations.no_plugins_title') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('dixlase-seo::admin/dixlase-seo/integrations.no_plugins_description') }}
                    </p>
                </div>
            </div>
        </div>
    @else
        <form id="dixlase-seo-integrations-form" method="POST" action="{{ route('dixlase-seo::admin.seo.integrations.update') }}">
            @csrf
            @method('PATCH')
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 space-y-6">
                @foreach ($plugins as $plugin)
                    <div class="flex items-start justify-between pb-4 border-b border-gray-200 dark:border-gray-700 last:border-0 last:pb-0">
                        <div class="flex-1 mr-4">
                            <div class="flex items-center gap-2 mb-1">
                                <i class="fas fa-cube text-blue-500 dark:text-blue-400"></i>
                                <span class="text-base font-semibold text-gray-900 dark:text-white">{{ $plugin['name'] }}</span>
                                <code class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded">{{ $plugin['slug'] }}</code>
                            </div>
                            @if ($plugin['description'] !== '')
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $plugin['description'] }}</p>
                            @endif
                        </div>
                        <div class="flex-shrink-0"
                            :aria-label="'{{ __('dixlase-seo::admin/dixlase-seo/integrations.enable_for_plugin', ['name' => $plugin['name']]) }}'">
                            <x-form-toggle
                                :name="'integration['.$plugin['slug'].']'"
                                :checked="old('integration.'.$plugin['slug'], $plugin['enabled'] ? '1' : '0')"
                            />
                        </div>
                    </div>
                @endforeach
            </div>
        </form>
    @endif
</section>

{{-- Orphaned meta cleanup (danger zone) --}}
<section class="mb-8">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
        <i class="fas fa-broom mr-2"></i>{{ __('dixlase-seo::admin/dixlase-seo/integrations.section_cleanup') }}
    </h2>

    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6">
        <h3 class="text-base font-semibold text-red-900 dark:text-red-200 mb-2">
            <i class="fas fa-trash-alt mr-2"></i>{{ __('dixlase-seo::admin/dixlase-seo/integrations.orphans_title') }}
        </h3>

        @if ($orphans['count'] === 0)
            <p class="text-sm text-gray-600 dark:text-gray-400">
                <i class="fas fa-check-circle text-green-500 mr-1"></i>
                {{ __('dixlase-seo::admin/dixlase-seo/integrations.orphans_none') }}
            </p>
        @else
            <p class="text-sm text-gray-700 dark:text-gray-300 mb-3">
                {{ __('dixlase-seo::admin/dixlase-seo/integrations.orphans_description', ['count' => $orphans['count']]) }}
            </p>

            @if (! empty($orphans['byPlugin']))
                <ul class="text-sm text-gray-600 dark:text-gray-400 mb-4 ml-4 list-disc space-y-1">
                    @foreach ($orphans['byPlugin'] as $row)
                        <li>
                            {{ __('dixlase-seo::admin/dixlase-seo/integrations.orphans_by_plugin', [
                                'slug' => $row['plugin_slug'],
                                'count' => $row['count'],
                            ]) }}
                        </li>
                    @endforeach
                </ul>
            @endif

            <button type="button"
                @click="openModal('purgeOrphansModal')"
                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                <i class="fas fa-trash mr-2"></i>
                {{ __('dixlase-seo::admin/dixlase-seo/integrations.purge_orphans_button') }}
            </button>

            <form id="dixlase-seo-purge-orphans-form"
                method="POST"
                action="{{ route('dixlase-seo::admin.seo.integrations.purge-orphans') }}"
                style="display: none;">
                @csrf
            </form>

            <x-ui-modal
                id="purgeOrphansModal"
                :title="__('dixlase-seo::admin/dixlase-seo/integrations.orphans_title')"
                :message="__('dixlase-seo::admin/dixlase-seo/integrations.purge_orphans_confirm', ['count' => $orphans['count']])"
                :confirm-label="__('dixlase-seo::admin/dixlase-seo/integrations.purge_orphans_button')"
                :cancel-label="__('common.cancel')"
                form="dixlase-seo-purge-orphans-form"
                icon-type="danger"
                confirm-color="red"
            />
        @endif
    </div>
</section>
@endsection

@section('save')
    @if (! empty($plugins))
        <x-admin.save-button form="dixlase-seo-integrations-form" />
    @endif
@endsection
